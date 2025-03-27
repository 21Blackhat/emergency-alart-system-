require('dotenv').config();
const WebSocket = require('ws');
const express = require('express');
const cors = require('cors');
const mysql = require('mysql');

const app = express();
app.use(cors());
app.use(express.json());

// MySQL Connection
const db = mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || '',
    database: process.env.DB_NAME || 'emergency_alert_system'
});

db.connect(err => {
    if (err) {
        console.error("Database error: " + err.message);
    } else {
        console.log("Connected to MySQL");
    }
});

// WebSocket Server
const wss = new WebSocket.Server({ port: 8080 });

let clients = {};

// When a responder connects, store their WebSocket connection
wss.on('connection', (ws, req) => {
    let userType = req.headers['user-type'];
    let userId = req.headers['user-id'];

    if (userType === "responder") {
        clients[userId] = ws;
        console.log(`Responder ${userId} connected.`);
    }

    ws.on('close', () => {
        delete clients[userId];
        console.log(`Responder ${userId} disconnected.`);
    });
});

// Function to notify responders
function notifyResponders(emergency) {
    Object.values(clients).forEach(client => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify(emergency));
        }
    });
}

// API to trigger WebSocket notifications
app.post('/notify', (req, res) => {
    const { location, user_name, emergency_id } = req.body;
    const emergencyData = { message: "🚨 New Emergency!", location, user_name, emergency_id };

    notifyResponders(emergencyData);
    res.send({ success: true });
});

// Start API server
app.listen(3000, () => console.log("WebSocket notification server running on port 3000"));

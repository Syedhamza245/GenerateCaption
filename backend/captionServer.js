const express = require('express');
const mysql = require('mysql2');
const app = express();
const cors = require('cors');

app.use(express.json());
app.use(cors());

const db = mysql.createConnection({
  host: 'localhost',
  user: 'root', 
  password: '', 
  database: 'auth-db', 
  port: 3307, 
});


db.connect((err) => {
  if (err) {
    console.error('Database connection error: ', err.stack);
    return;
  }
  console.log('Connected to MySQL database');
});

app.post('/save-caption', (req, res) => {
  const { caption } = req.body;

  if (!caption) {
    return res.status(400).json({ message: 'Caption is required' });
  }

  const query = 'INSERT INTO captions (caption_text) VALUES (?)';
  
  db.query(query, [caption], (err, result) => {
    if (err) {
      console.error('Error inserting caption: ', err);
      return res.status(500).json({ message: 'Failed to save caption to database' });
    }
    console.log('Caption saved:', result);
    res.status(200).json({ message: 'Caption saved successfully' });
  });
});

const PORT = 4000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});

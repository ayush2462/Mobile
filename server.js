const express = require('express');
const bodyParser = require('body-parser');
const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');
const app = express();
const port = 3000;

app.use(express.static(path.join(__dirname, 'public')));
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'user-login.html'));
});
app.get('/dev', (req, res) =>{
  res.sendFile(path.join(__dirname, 'public', 'data-entry.html'));
});

app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(__dirname));

const filePath = './submissions.xlsx';

// Developer submits data
app.post('/submit-entry', (req, res) => {
  const { name, phone, status } = req.body;

  let workbook, worksheet;
  if (fs.existsSync(filePath)) {
    workbook = XLSX.readFile(filePath);
    worksheet = workbook.Sheets[workbook.SheetNames[0]];
  } else {
    workbook = XLSX.utils.book_new();
    worksheet = XLSX.utils.json_to_sheet([]);
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
  }

  const data = XLSX.utils.sheet_to_json(worksheet);

  const exists = data.find(
    entry =>
      String(entry.Name).toLowerCase().trim() === name.toLowerCase().trim() &&
      String(entry.Phone).trim() === phone.trim()
  );
  

  if (exists) {
    return res.send(`<script>alert("Entry already exists!"); window.history.back();</script>`);
  }

  data.push({ Name: name, Phone: phone, Status: status });

  const updatedSheet = XLSX.utils.json_to_sheet(data);
  workbook.Sheets[workbook.SheetNames[0]] = updatedSheet;
  XLSX.writeFile(workbook, filePath);

  res.send(`<script>alert("Entry added successfully!"); window.history.back();</script>`);
});

// User checks status
app.post('/check-status', (req, res) => {
  const { name, phone } = req.body;

  if (!fs.existsSync(filePath)) {
    return res.send('<h2 style="color:red;">Database not found.</h2>');
  }

  const workbook = XLSX.readFile(filePath);
  const worksheet = workbook.Sheets[workbook.SheetNames[0]];
  const data = XLSX.utils.sheet_to_json(worksheet);

  const match = data.find(
    entry =>
      String(entry.Name).toLowerCase().trim() === name.toLowerCase().trim() &&
      String(entry.Phone).trim() === phone.trim()
  );
  

  if (match) {
    res.send(`
      <div style="font-family:sans-serif;text-align:center;margin-top:50px;">
        <h2>Status Found ✅</h2>
        <p>Name: <strong>${match.Name}</strong></p>
        <p>Phone: <strong>${match.Phone}</strong></p>
        <p>Status: <strong style="color:green;">${match.Status}</strong></p>
      </div>
    `);
  } else {
    res.send(`
      <div style="font-family:sans-serif;text-align:center;margin-top:50px;">
        <h2 style="color:red;">No record found</h2>
        <p>Please verify your input and try again.</p>
      </div>
    `);
  }
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});

const express = require('express');
const path = require('path');
const bodyParser = require('body-parser');
const cors = require('cors');
const sgMail = require('@sendgrid/mail');
const { env } = require('process');

const dotenv = require('dotenv');
dotenv.config()

const app = express();
const PORT = process.env.PORT || 8080;

sgMail.setApiKey(process.env.SENDGRID_API_KEY); // Set

// Middleware
app.use(bodyParser.json({ limit: '10mb' }));
app.use(bodyParser.urlencoded({ limit: '10mb', extended: true }));
app.use(cors());
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public'))); // Serves index.html
// app.get('*', (req, res) => {
//   res.sendFile(path.join(__dirname, 'public', 'index.html'));
// });

app.post('/send-email', async (req, res) => {
  const { name, email, subject, message, file } = req.body;

  const msg = {
    to: 'shantanu.habade@gmail.com',
    from: 'inquiry.sarasenterprises@gmail.com',
    subject: subject || 'New Inquiry from Website',
    content: [
      {
        type: 'text/plain',
        value: `Name: ${name}\nEmail: ${email}\nMessage: ${message}`,
      },
    ],
    attachments: file ? [
      {
        content: file.content,
        filename: file.filename,
        type: file.type,
        disposition: 'attachment',
      },
    ] : [],
  };

  try {
    await sgMail.send(msg);
    res.status(200).json({ success: true, message: 'Email sent successfully!' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ success: false, message: 'Email sending failed.' });
  }
});

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});

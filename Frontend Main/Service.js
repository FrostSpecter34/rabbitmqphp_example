const express = require('express');
const bodyParser = require('body-parser');
const nodemailer = require('nodemailer');

const app = express();
app.use(bodyParser.json());

const transporter = nodemailer.createTransport({
    service: 'Gmail', // Use your email service
    auth: {
        user: 'your-email@gmail.com',  // Replace with your email
        pass: 'your-email-password',  // Replace with your password or app-specific password
    },
});

app.post('/send-code', (req, res) => {
    const { email } = req.body;
    const verificationCode = Math.floor(100000 + Math.random() * 900000);

    const mailOptions = {
        from: 'your-email@gmail.com',
        to: email,
        subject: 'Your Verification Code',
        text: `Your verification code is: ${verificationCode}`,
    };

    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            return res.status(500).json({ message: 'Error sending email.' });
        }
        res.status(200).json({ message: 'Verification code sent!' });
    });
});

app.listen(3000, () => console.log('Server running on port 3000'));

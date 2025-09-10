Web Attack Demo - Instructions for Use and Testing

1. Setup Instructions

Prerequisites: 
	Operating System: Windows, macOS, or Linux
	XAMPP (or any Apache + MySQL stack)
	PHP (at least version 7.4)
	MySQL
	Web Browser (Chrome, Firefox, or Edge)

 Step-by-Step Guide:

	1. Install XAMPP (if not already installed)
- Download XAMPP from [Apache Friends](https://www.apachefriends.org/index.html)
- Install and press button 'start' in **Apache** and **MySQL** from the XAMPP Control Panel

	2. Setup the Database
- Open phpMyAdmin (`http://localhost/phpmyadmin`)
- Go to or create a new database named `mysql` and then click tab `SQL`
- Run the following SQL query, which is provided in the source code (mysql.sql), to create the tables used in the demo:
  ```
  CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE account_balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00
);
  ```

	3. Deploy the Project
	- Copy the `demo` project folder to `..\xampp\htdocs\`
	- Ensure the directory structure is:
 	 ```
 	 ..\xampp\htdocs\demo\
	├── authenticate.php
	├── db.php
      	├── index.php
	├── login.php
	├── logout.php
	├── mysql.sql
	├── register.php
	├── registration.php
	├── transact.php
	├── vuln_authenticate.php
	├── vuln_registration.php
	├── vuln_transact.php
  	```

	4. Configure Database Connection
	- Open `db.php` and verify the connection details:
 	 ```php
  	$mysqli = new mysqli("localhost", "root", "", "mysql");
 	 ```
	- If you are using a different port (e.g., `3306`), update the connection string accordingly.

	5. Start the Server
	- Open XAMPP Control Panel and ensure Apache & MySQL are running.
	- Open a web browser and go to:
  	```
  	http://localhost/demo/index.php
  	```

2. Testing the XSS Attack
   1. Change source code:
   - Open `registration.php`:
   +Change
  ```	$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); //htmlspecialchars avoid XSS 
	$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
	$confirm_password = htmlspecialchars($_POST['confirm_password'], ENT_QUOTES, 'UTF-8');
	$bal = htmlspecialchars($_POST['bal'], ENT_QUOTES, 'UTF-8');
  ```
  into
  ```
	$name = $_POST['name']; 	// easy for XSS attack
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$bal = $_POST['bal'];
  ```
   + Remove 
	```
	// Input validation to avoid XSS
	if (!preg_match("/^[a-zA-Z0-9 ]*$/", $name)) {
  	die("Invalid name! Only letters and numbers allowed.");}

	```
  -Open `authenticate.php`: 
  Change 
  ```
    	$username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
   	$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
  ``` 
  into
  ```
    	$username = ($_POST['username']);  //remove htmlspecialchars,ENT_QUOTES, 'UTF-8' to test XSS
    	$password = ($_POST['password']);
  ```

     2. Register a normal account(click"Register for an account")
     - Enter any text (e.g., `Thinh`) in the input field and click **Register(Secure)**.
     - The information should be stored in the database named "mysql" and your account displayed on the page.

     3. Test an XSS Attack
     - In the login.php, create an account.
     - Enter the following script in the input field:
     ```
     <script>alert('XSS Attack!');</script>
     ```
     - Click **Register(Secure)**.
     - If the application is vulnerable, an alert box will pop up displaying `XSS Attack!`.

3. Fixing the XSS Vulnerability

    1. Apply Output Encoding And Input Validation.
    - Open `registration.php` and update the display code:
    ```
  	$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); //htmlspecialchars avoid XSS 
	$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
	$confirm_password = htmlspecialchars($_POST['confirm_password'], ENT_QUOTES, 'UTF-8');
	$bal = htmlspecialchars($_POST['bal'], ENT_QUOTES, 'UTF-8');

    ```
   - Then remove 
   ```
	$name = $_POST['name']; // easy for XSS attack
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$bal = $_POST['bal'];
   ```
  - Adding code
  ``` 
	// Input validation to avoid XSS
	if (!preg_match("/^[a-zA-Z0-9 ]*$/", $name)) {
    	die("Invalid name! Only letters and numbers allowed.");}
  ```
  - This will encode special characters, preventing script execution.

  2. Retest the XSS Attack
  - Submit the same `<script>alert('XSS Attack!');</script>` in registration.
  - The script shouldnot be displayed and the line "Invalid name! Only letters and numbers allowed." will appear avoiding XSS attack.

4. Additional Notes
- Ensure that you restart Apache after making any changes.
- If using a different MySQL port, update `db.php` accordingly.

5. Testing SQL Injection attack(In-Band SQL Injection)
	a. Change source code:
	We already designed 2 button lead to 2 source codes (vulnerable and secure) in both login page, and register page. So no changes are necessary.
	b. Register a normal account(click"Register for an account")
     	- Create 2 new accounts for testing,using different register button to make sure both button working.
     	- The information should be stored in the database named "mysql" and your account displayed on the page.
	c. Test 2 types of SQLi attacks:
	- At the register page:  
  		+ Enter the following command in the username field: `' UNION SELECT 1 --  ` (remember to add two spaces after the double dash to avoid syntax errors). Enter any input for the password. However it must be a number for balance field
  		+ First, choose the **Secure** button, then the **Vulnerable** button to see the differences: the **Secure** version won't accept the username, while the **Vulnerable** version will display a fatal error.  
  		+ Update the command to: `' UNION SELECT 1,2,3 --  ` (again, ensure two spaces after the double dash) and hit the **Vulnerable** button. This time, it won't show a fatal error because you've correctly guessed the number of columns in the banking database.
	- At the login page:
		+ Enter your account 2 times by different button to make sure it all work, then logout.
		+ Enter the following command in the username field: `' OR 1=1 --  ` (remember to add two spaces after the double dash to avoid syntax errors). Enter any input for the password.
		+ Use that command for both button: the **Secure** version won't accept the username, while the **Vulnerable** version will let you in with out provide any exist username or password.

6. Testing/Simulating a CSRF attack
- Any CSRF Attacks can be tested and done using the vulnerable versions of data processing pages: vuln_authenticate.php; vuln_registration.php; vuln_transact.php
- For example, for an attack on an account balance as demonstrated in the video, you'll need the victim to somehow open the link you manipulated for them. The link is formatted as follows: localhost/demo/vuln_transact.php?bId=(INTEGER)&amt=(INTEGER)
- You can then replace the number at bId and amt to your preference and test the attack. And it definitely comes with a prerequisite that you have the necessary user records created first in the database for valid targets.
- You can test the same method but for the secured version of the pages, by using their invulnerable counter parts. For example: localhost/demo/transact.php?bId=(INTEGER)&amt=(INTEGER)
- This would not work as the page is protected by a CSRF token




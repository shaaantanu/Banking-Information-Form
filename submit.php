<?php
// Retrieve form data
$accountHolderName = $_POST['account_holder_name'];
$accountNumber = $_POST['account_number'];
$accountType = $_POST['account_type'];
$bankName = $_POST['bank_name'];
$branchName = $_POST['branch_name'];
$identificationProof = $_FILES['identification_proof'];
$occupation = $_POST['occupation'];
$incomeDetails = $_POST['income_details'];
$transactionHistory = $_POST['transaction_history'];
$balance = $_POST['balance'];

// Validate form inputs
$errors = [];

if (empty($accountHolderName)) {
    $errors[] = "Account Holder Name is required.";
}

if (empty($accountNumber)) {
    $errors[] = "Account Number is required.";
}

if (empty($accountType)) {
    $errors[] = "Account Type is required.";
}

if (empty($bankName)) {
    $errors[] = "Bank Name is required.";
}

if (empty($branchName)) {
    $errors[] = "Branch Name is required.";
}

// Additional validation for other attributes

if (!empty($errors)) {
    // Handle validation errors, display error messages, or redirect back to the form page
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create a table in the database (if it doesn't exist already)
$sql = "CREATE TABLE IF NOT EXISTS banking_info (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_holder_name VARCHAR(255) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    account_type VARCHAR(50) NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    branch_name VARCHAR(255) NOT NULL,
    identification_proof VARCHAR(255) NOT NULL,
    occupation VARCHAR(255) NOT NULL,
    income_details VARCHAR(255) NOT NULL,
    transaction_history VARCHAR(255) NOT NULL,
    balance DECIMAL(10,2) NOT NULL
)";

if ($conn->query($sql) === false) {
    echo "Error creating table: " . $conn->error;
    $conn->close();
    exit;
}

// Move the uploaded identification proof file to a desired location
$targetDir = "uploads/";
$identificationProofPath = $targetDir . basename($identificationProof["name"]);

if (!move_uploaded_file($identificationProof["tmp_name"], $identificationProofPath)) {
    echo "Error uploading identification proof file.";
    $conn->close();
    exit;
}

// Insert the validated applicant data into the database table
$sql = "INSERT INTO banking_info (account_holder_name, account_number, account_type, bank_name, branch_name,
        identification_proof, occupation, income_details, transaction_history, balance)
        VALUES ('$accountHolderName', '$accountNumber', '$accountType', '$bankName', '$branchName',
        '$identificationProofPath', '$occupation', '$incomeDetails', '$transactionHistory', '$balance')";

if ($conn->query($sql) === false) {
    echo "Error inserting data: " . $conn->error;
} else {
    echo "Data inserted successfully!";
}

$conn->close();
?>

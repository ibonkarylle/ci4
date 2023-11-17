<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RestFul\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MainModel;
use App\Models\AdminModel;
use App\Models\PpoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MainController extends ResourceController
{
    public function index(){
        return view('upload');
    }
    public function getUsers(){

        $main = new MainModel();
        $data = $main->findall();
        return $this->respond($data,200);
    }

    public function getAdmins(){
        $main = new AdminModel();
        $data = $main->findall();
        return $this->respond($data,200);
    }

    public function getPpo(){
        $main = new PpoModel();
        $data = $main->findall();
        return $this->respond($data,200);
    }

    public function save(){
        $json = $this->request->getJSON();
        $data = [
            'username' => $json->username,
            'password' => $json->password,
            'confirmpassword' => $json->confirmpassword,
            'office' => $json->office,
            'phone_no' => $json->phone_no,
            'email' => $json->email,
        ];
        $main = new MainModel();
        $r = $main->save($data);
        return $this->respond($r,200);
    }
    
    
   

    public function login(){
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $main = new MainModel();
    $user = $main->where('email', $email)->first();


    // If login is successful
    return $this->respond([
        'success' => true,
        'message' => 'Login successful',
        'user' => $user,
    ], 200);
}

public function generateExcel()
{
    $model = new PpoModel();
    $data = $model->getData(); // Assuming you have a method to fetch data from the database in your model

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add headers
    $headers = array_keys($data[0]);
    foreach ($headers as $colIndex => $header) {
        $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
    }

    // Add data
    foreach ($data as $rowIndex => $rowData) {
        $colIndex = 0;
        foreach ($rowData as $value) {
            $colIndex++;
            $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex + 2, $value);
        }
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'exported_data.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    
}


public function upload()
{
    $request = $this->request;

    // Debugging to check what data is received
    log_message('debug', print_r($request->getPost(), true));
    log_message('debug', print_r($_FILES, true));
    // Get the file
    $file = $request->getFile('file');

    // Move the file to the writable/uploads directory
    $uploadsDirectory = FCPATH . 'uploads';  // Correct path using FCPATH

    // Check if the directory exists, if not, create it
    if (!is_dir($uploadsDirectory)) {
        mkdir($uploadsDirectory, 0777, true);
    }

    $file->move($uploadsDirectory);

    // Insert the user information into the database
    $filePath = 'uploads/' . $file->getName();
    
    $userData = [
        'username' => $request->getPost('username'),
        'password' => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
        'confirmpassword' => $request->getPost('confirmpassword'),
        'office' => $request->getPost('office'),
        'phone_no' => $request->getPost('phone_no'),
        'email' => $request->getPost('email'),
        'image' => $filePath,
    ];

    // Assuming you have a model named MainModel, you can use it to insert data into the database
    $main = new MainModel();
    $main->insert($userData);

    // Redirect back to the form with a success message
    return redirect()->to('/')->with('success', 'Registration successful!');
}

public function sendEmail()
{
    try {
        // Get JSON data from the request
        $formData = $this->request->getJSON();

        // Load the email library
        $email = \Config\Services::email();

        // Set email parameters
        $email->setTo('ashlyomanada@gmail.com');
        $email->setFrom($formData->sender);
        $email->setSubject('Request Form');
        $message = 'This is a request form from username: ' . $formData->username;
        $email->setMessage($message);

        // Send email
        if ($email->send()) {
            return $this->response->setJSON(['message' => 'Email sent successfully.']);
        } else {
            log_message('error', 'Email failed to send. Error: ' . $email->printDebugger(['headers']));
            return $this->response->setJSON(['error' => 'Email failed to send.']);
        }
    } catch (\Exception $e) {
        // Log other exceptions
        log_message('error', 'Exception: ' . $e->getMessage());
        return $this->response->setJSON(['error' => 'Internal Server Error']);
    }
}






}
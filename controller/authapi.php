<?php


class Login
{
    public $username;
    public $password;
    public $url;
    public $urlU;
    public $ruta;
    public $token;
    public $file;

    // Constructor
    public function __construct()
    {
        //PRUEBA
        // $this->username = 'delta@amovil.co';
        // $this->password = 'Delta2019$';
        // $this->url = 'https://fepruebas.amovil.co:8080/api/auth/token/';
        // $this->urlU = 'https://fepruebas.amovil.co:8080/api/';
        // $this->ruta = $this->urlU . "integration/upload/";
        //PRODUCCION
        $this->username = 'delta@amovil.co';
        $this->password = 'Felam2020$';
        $this->url = 'https://felam2.amovil.co/api/auth/token/';
        $this->urlU = 'https://felam2.amovil.co/api/';
        $this->ruta = $this->urlU . "integration/upload/";
        $this->auth();
    }

    public function auth()
    {
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',

        );
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=" . $this->username . "&password=" . $this->password . ";");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $token = $result['token'];
        $this->token = $token;
    }
    public function Uploader($file)
    {
        date_default_timezone_set("America/Bogota");
        $fecha = Date('Y-m-d');
        $token = "Token" . " " . $this->token;
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile(__DIR__ . DIRECTORY_SEPARATOR . $file, $mime, $name);
        $data = array(
            "data" => $output,
        );
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization:' . $token,
                'Content-Type: multipart/form-data',
            )
        );
        curl_setopt($ch, CURLOPT_URL, $this->ruta);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);
        if ($result) {
            unlink($file);
            header("Location: ../view/exit.php");;
            die();
        } else {
            header("Location: ../view/err.php");
            die();
        }
    }
}
$login = new Login;
$login->auth();
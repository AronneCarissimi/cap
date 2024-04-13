<?php
//elabora header
$metodo=$_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

do {
    $first = array_shift($uri);
} while ($first != 'api');


//print_r($uri);




//legge il tipo di contenuto inviato dal client
if (isset($_SERVER["CONTENT_TYPE"])){
$ct=$_SERVER["CONTENT_TYPE"];
$type=explode("/",$ct);
}
else{
    $type=array();
    $type[0]="application";
    $type[1]="json";
}
//legge il tipo di contenuto di ritorno richiesto dal client
$retct=$_SERVER["HTTP_ACCEPT"];
$ret=explode("/",$retct);

//echo $type[1];
//print_r($uri);
//echo "metodo-->".$metodo;

//connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}




switch ($metodo) {
    case 'GET':
        
    if ($uri[0]!=""){
        $sql = "SELECT * FROM cap WHERE comune='".$uri[0]."'";
        $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $rows = array();
                    while($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    header("Content-Type: ".$retct);
                    if ($type[1]=="json"){
                        echo json_encode($rows);
                    }
                    if ($type[1]=="xml"){
                        $xml = new SimpleXMLElement('<root/>');
                        array_walk_recursive($rows, array ($xml, 'addChild'));    
                        echo $xml->asXML();
                    }
                } else {
                    echo "0 results";
                }
            }
            else{
                $sql = "SELECT * FROM cap";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $rows = array();
                    while($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    header("Content-Type: ".$retct);
                    if ($type[1]=="json"){
                        echo json_encode($rows);
                    }
                    if ($type[1]=="xml"){
                        $xml = new SimpleXMLElement('<root/>');
                        array_walk_recursive($rows, array ($xml, 'addChild'));    
                        echo $xml->asXML();
                    }
                } else {
                    echo "0 results";
                }
            }
        
        break;

    case 'POST':
        //recupera i dati dall'header
        $body=file_get_contents('php://input');

        //esempio body {"comune":"bonate","cap":"24040"}

        //converte in array associativo
        if ($type[1]=="json"){
            $data = json_decode($body,true);
        }
        if ($type[1]=="xml"){
            $xml = simplexml_load_string($body);
            $json = json_encode($xml);
            $data = json_decode($json, true);
        }

        $sql = "INSERT INTO cap (comune, cap) VALUES ('".$data["comune"]."', '".$data["cap"]."')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode($data);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    break;
    case 'PUT':
        //recupera i dati dall'header
        $body=file_get_contents('php://input');
    
        //converte in array associativo
        if ($type[1]=="json"){
            $data = json_decode($body,true);
        }
            if ($type[1]=="xml"){
            $xml = simplexml_load_string($body);
            $json = json_encode($xml);
            $data = json_decode($json, true);
        }
        //esempio body [{"ID":"6","comune":"bonate","cap":"24040"}]
        $sql= "UPDATE `cap` SET `comune` = '".$data["comune"]."', `cap` = '".$data["cap"]."' WHERE `cap`.`id` = ".$data["id"];

        if ($conn->query($sql) === TRUE) {
            echo json_encode($data);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        

        break;

    case 'DELETE':
    //recupera i dati dall'header
    $body=file_get_contents('php://input');
    
    //converte in array associativo
    if ($type[1]=="json"){
        $data = json_decode($body,true);
    }
        if ($type[1]=="xml"){
        $xml = simplexml_load_string($body);
        $json = json_encode($xml);
        $data = json_decode($json, true);
    }
    $sql= "DELETE FROM `cap` WHERE `cap`.`id` = ".$data["id"];

    if ($conn->query($sql) === TRUE) {
        echo json_encode($data);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
        break;

    default:
        echo "metodo non gestito";
        http_response_code(404);
        break;

}
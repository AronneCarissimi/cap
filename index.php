<?php





//elabora header
$metodo=$_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

//legge il tipo di contenuto inviato dal client
$ct=$_SERVER["CONTENT_TYPE"];
$type=explode("/",$ct);

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
        
    if ($uri[3]!=""){
        $sql = "SELECT comune,cap FROM cap WHERE comune='".$uri[3]."'";
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
                    echo "0 resultssssss";
                }
            }
            else{
                $sql = "SELECT comune,cap FROM cap";
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
        echo "post";
        http_response_code(404);
        break;

    case 'PUT':
        echo "put";
        http_response_code(404);
        break;

    case 'DELETE':
        echo "delete";
        http_response_code(404);
        break;

    default:
        echo "metodo non gestito";
        http_response_code(404);
        break;

}







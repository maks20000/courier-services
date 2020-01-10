<?
require_once 'db.php';
$link = mysqli_connect($host, $user, $password, $database) 
    or die("Ошибка " . mysqli_error($link));
        $array=[];
        $query ="SELECT * FROM fs_region ORDER BY name";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
        $i=0;
        while ($row = mysqli_fetch_row($result)) {
            $array['regions'][$i]['id']=$row[0];
            $array['regions'][$i]['name']=$row[4];
            $array['regions'][$i]['code']=$row[3];
            $i++;
        }
        $query ="SELECT * FROM fs_city ORDER BY name";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
        $i=0;
        while ($row = mysqli_fetch_row($result)) {
            $array['city'][$i]['id_region']=$row[1];
            $array['city'][$i]['name']=$row[2];
            $i++;
        }


        

        file_put_contents("cityList.txt",json_encode ($array));


        mysqli_close($link);
?>
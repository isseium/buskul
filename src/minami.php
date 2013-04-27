<?php

    /*
    ** htmlSQL - Example 1
    **
    ** Shows a simple query
    */
    
    include_once("htmlSQL/snoopy.class.php");
    include_once("htmlSQL/htmlsql.class.php");
    
    $wsql = new htmlsql();
    
    // connect to a URL
    if (!$wsql->connect('url', 'http://gps.iwatebus.or.jp/bls/pc/busiti_jk.jsp?jjg=1&jtr=143&kjg=3&ktr=241&don=1')){
        print 'Error while connecting: ' . $wsql->error;
        exit;
    }
    
    /* execute a query:
        
       This query extracts all links with the classname = nav_item   
    */
    if (!$wsql->query('SELECT * FROM tr WHERE $class == "busstop"' )){
        print "Query error: " . $wsql->error; 
        exit;
    }

    // show results:
    foreach($wsql->fetch_array() as $row){
        $converted = "<root>" . mb_convert_encoding($row["text"], "utf8", "sjis") . "</root>";
        $espaped = str_replace(array("\r\n","\r","\n"), '', $converted);
        $escaped = str_replace("&nbsp;", " ", $converted);
        $escaped = str_replace("nowrap", "", $escaped);
//       $escaped = str_replace("", "", $escaped);
//       $escaped = str_replace("", "", $escaped);
        $escaped = preg_replace('/<img (.*)>/', '<img ${1} ></img>',  $escaped);
#        $escaped = preg_replace('/[ \s]+/', '',  $escaped);
        $xml = simplexml_load_string($escaped);
        var_dump($xml);
        
        /* 
        $row is an array and looks like this:
        Array (
            [href] => /feedback.htm
            [class] => nav_item
            [tagname] => a
            [text] => Feedback
        )
        */
        
    }
    
?>

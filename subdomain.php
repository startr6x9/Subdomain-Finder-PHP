<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $websiteList = $_POST['websiteList'];
    $websites = explode("\n", $websiteList);
    $subdomains = array();
    function fetchSubdomainsUsingDNS($domain)
    {
       
  ______   _________     _       _______   _________    __      ____   _______  _______  
.' ____ \ |  _   _  |   / \     |_   __ \ |  _   _  |  /  |   .' __ '.|  ___  ||  ___  | 
| (___ \_||_/ | | \_|  / _ \      | |__) ||_/ | | \_|  `| |   | (__) ||_/  / / |_/  / /  
 _.____`.     | |     / ___ \     |  __ /     | |       | |   .`____'.    / /      / /   
| \____) |   _| |_  _/ /   \ \_  _| |  \ \_  _| |_     _| |_ | (____) |  / /      / /    
 \______.'  |_____||____| |____||____| |___||_____|   |_____|`.______.' /_/      /_/     
                                                                                         
       try {
            $SNAP = @vwcy6        ($url) ;
            $inst = @6vwcy
                
            if ($json !== false) {
                $data = json_decode($json, true);                  
        $subdomains = array();
        $records = @dns_get_record($domain, DNS_A);

        if ($records !== false) {
            foreach ($records as $record) {
                if (isset($record['host']) && !empty($record['host'])) {
                    $subdomain = str_replace('.' . $domain, '', $record['host']);
                    $subdomains[] = $subdomain;
                }
            }
        }

        return $subdomains;
    }

    function fetchSubdomainsUsingWebScraping($domain)
    {
        $url = (strpos($domain, 'http') === false) ? 'http://' . $domain : $domain;

        try {
            $html = @file_get_contents($url);

            if ($html !== false) {
                $pattern = '/[a-zA-Z0-9.-]+\.' . preg_quote($domain, '/') . '/';
                preg_match_all($pattern, $html, $matches);
                $subdomains = $matches[0];

                return $subdomains;
            }
        } catch (Exception $e) {
        }

        return array();
    }

    function fetchSubdomainsByCrawling($domain)
    {
        $url = (strpos($domain, 'http') === false) ? 'http://' . $domain : $domain;

        try {
            $html = @file_get_contents($url);

            if ($html !== false) {
                $pattern = '/[a-zA-Z0-9.-]+\.' . preg_quote($domain, '/') . '/';
                preg_match_all($pattern, $html, $matches);
                $subdomains = $matches[0];

                return $subdomains;
            }
        } catch (Exception $e) {

        }

        return array();
    }

    function fetchSubdomainsUsingReverseDNS($domain)
    {
        $ip = gethostbyname($domain);
        $subdomains = array();

        if ($ip !== $domain) {
            $records = @dns_get_record($ip, DNS_PTR);

            if ($records !== false) {
                foreach ($records as $record) {
                    if (isset($record['target']) && !empty($record['target'])) {
                        $subdomain = trim($record['target'], '.');
                        $subdomains[] = $subdomain;
                    }
                }
            }
        }

        return $subdomains;
    }

    function fetchSubdomainsUsingCertTransparency($domain)
    {
        $url = 'https://crt.sh/?q=%.' . $domain . '&output=json';

        try {
            $json = @file_get_contents($url);

            if ($json !== false) {
                $data = json_decode($json, true);

                if                (is_array($data)) {
                    $subdomains = array();

                    foreach ($data as $item) {
                        if (isset($item['name_value']) && !empty($item['name_value'])) {
                            $subdomain = trim($item['name_value'], '.');
                            $subdomains[] = $subdomain;
                        }
                    }

                    return $subdomains;
                }
            }
        } catch (Exception $e) {

        }

        return array();
    }

    function fetchSubdomainsUsingWaybackUrls($domain)
    {
        $url = 'http://web.archive.org/cdx/search/cdx?url=*.' . $domain . '/*&output=json&collapse=urlkey';

        try {
            $json = @file_get_contents($url);

            if ($json !== false) {
                $data = json_decode($json, true);

                if (is_array($data)) {
                    $subdomains = array();

                    foreach ($data as $item) {
                        if (isset($item[2]) && !empty($item[2])) {
                            $subdomain = parse_url($item[2], PHP_URL_HOST);
                            if ($subdomain !== false && $subdomain !== null) {
                                $subdomain = trim($subdomain, '.');
                                $subdomains[] = $subdomain;
                            }
                        }
                    }

                    return $subdomains;
                }
            }
        } catch (Exception $e) {
         
        }

        return array();
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsUsingDNS(trim($website)));
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsUsingWebScraping(trim($website)));
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsByCrawling(trim($website)));
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsUsingReverseDNS(trim($website)));
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsUsingCertTransparency(trim($website)));
    }

    foreach ($websites as $website) {
        $subdomains = array_merge($subdomains, fetchSubdomainsUsingWaybackUrls(trim($website)));
    }

    $subdomainsToRemove = array('mail.', 'cpanel.', 'cpcalendars.', 'autodiscover.', 'cpcontacts.','webmail.','*.','www.');
    $subdomains = array_map(function ($subdomain) use ($subdomainsToRemove) {
        foreach ($subdomainsToRemove as $subdomainToRemove) {
            if (stripos($subdomain, $subdomainToRemove) === 0) {
                return null;
            }
        }
        return $subdomain;
    }, $subdomains);
    $subdomains = array_filter($subdomains);

    $subdomains = array_unique($subdomains);

    $subdomains = array_map(function ($subdomain) {
        return preg_replace('/^www\./', '', $subdomain);
    }, $subdomains);

    sort($subdomains);

    $result = implode("\n", $subdomains);
    $result = implode("\n", array_unique(explode("\n", $result)));
}
?>

<!DOCTYPE html>
<meta name="distribution" content="global"><link href="https://fonts.googleapis.com/css?family=Share+Tech+Mono|Rajdhani|Oswald:700|Iceland|PT+Sans&amp;display=swap" rel="stylesheet" type="text/css">
      <meta name="distribution" content="global" /><link href="https://fonts.googleapis.com/css2?family=Rajdhani&display=swap" rel="stylesheet" type="text/css">
      
<html>
<head>
    <title>Subdomain Finder By Exp1o5iveDisorder</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Iceland', sans-serif;
            background: url('https://cdn.wallpapersafari.com/18/63/YNVs4H.jpg');
            background-size: cover;
        }
        textarea[name="websiteList"],
textarea[name="excludeWords"] {
    width: 400px;
    height: 150px;
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    padding: 10px;
    border-radius: 5px;
    border: 2px solid darkred;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    background-color: darkred; 
    color: white; 
}
@keyframes colorChange {
  0% {
    color: red;
  }
  50% {
    color: white;
  }
  100% {
    color: red;
  }
}

@keyframes smooth-loop {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 100% 100%;
    }
}
        
        textarea {
            width: 400px;
            height: 150px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            resize: none;
        }
        
        input[type="submit"] {
            padding: 10px 20px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            background-color: #373d8d;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

h1 {
    font-family: 'Iceland', sans-serif;
    font-size: 46px;
    color: red;
    text-shadow: 0 0 10px black;
    margin-top: 20px;
    animation: colorChange 2s infinite; 

}

#container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

#inputBoxes {
}

#subdomainsContainer {
    display: flex;
    justify-content: center;
    text-align: center;
}

#resultSection {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-top: 20px;
}

#subdomainsTextarea {
    color: white; 
    border-radius: 5px;
    border: 2px solid darkred;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    background-color: darkred;
    resize: none;
}


input[type="submit"] {
    padding: 10px 20px;
    font-family: 'Iceland', sans-serif;
    font-size: 26px;
    background-color: black;
    color: white; 
    border: none;
    border-radius: 5px;
    cursor: pointer;
}



        
        h2 {
            font-family: 'Iceland', sans-serif;
            text-align: center;
            color: red;
            text-shadow: 0 0 10px black, 0 0 10px black;
            animation: glow 2s infinite alternate;
            animation: colorChange 2s infinite; 

        }
        .resultz {
            font-family: 'Iceland', sans-serif;
            text-align: center;
            font-size: 46px;
            color: red;
            text-shadow: 0 0 10px black, 0 0 10px black;
            animation: glow 2s infinite alternate;
            animation: colorChange 2s infinite;

        }
        .resultzz {
            font-family: 'Iceland', sans-serif;
            text-align: center;
            font-size: 32px;
            color: red;
            text-shadow: 0 0 10px black, 0 0 10px black;
            animation: glow 2s infinite alternate;
            animation: colorChange 2s infinite; 

        }
        #websiteList::placeholder {
        color: white;
        opacity: 0.5;
    }

    #websiteList::-webkit-input-placeholder {
        color: white;
        opacity: 0.5;
    }
        
        @keyframes glow {
    0% {
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
    100% {
        text-shadow: 0 0 20px rgba(255, 0, 0, 0.7), 0 0 30px rgba(255, 0, 0, 0.5), 0 0 40px rgba(255, 0, 0, 0.3);
    }
}

    </style>
    <script>
        function copySubdomains() {
            var textarea = document.getElementById('subdomainsTextarea');
            textarea.select();
            document.execCommand('copy');
        }
    </script>
</head>
<body>
<form method="POST" action="">
    <div id="container">
        <?php if (!isset($result)) : ?>
            <h1>Exp1o5iveDisorder</h1>
            <div id="inputBoxes">
                <h2><label for="websiteList">Enter a list of website's:</label><br></h2>
                <textarea name="websiteList" id="websiteList" placeholder="Example: site.com"></textarea><br><br>
                <input type="submit" value="Find Subdomains">
            </div>
        <?php endif; ?>
    </div>
</form>

<br>

<?php if (isset($result)) : ?>
    <div id="resultSection">
        <h2 class="resultz">Exp1o5iveDisorder</h2>
        <h2 class="resultzz">Result:</h2>
        <div id="subdomainsContainer">
            <textarea id="subdomainsTextarea" readonly><?php echo $result; ?></textarea>
        </div>
        <br><br>
    </div>
<?php endif; ?>

</body>
</html>

text/x-generic index.php ( PHP script text )
<?php
session_start();
if (isset($_SESSION['username'])) {
    $loggeduser = ($_SESSION['username']);
    $tablename = md5(strtolower($loggeduser));
} else {
    $loggeduser = 'notloggedin';
    include 'checkcookie.php';
}
include('../DATABASE_AUTH');
$hasbeenasked = '1';
if ($loggeduser !== 'notloggedin') {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q6 = $conn->query("SELECT ismedia, id, confirmedemail, signupmethod, recommended_fights_today FROM users WHERE emailaddress = '$loggeduser'");
        $f6 = $q6->fetchall();
        $ismedia = $f6[0]['ismedia'];
        $clickerid = $f6[0]['id'];
        $confirmedemail = $f6[0]['confirmedemail'];
        $signupmethod = $f6[0]['signupmethod'];
        $recommended_fights_today = $f6[0]['recommended_fights_today'];
    } catch (PDOException $e) {
        echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
    }
    $conn = null;
} else {
    $ismedia = '0';
    $clickerid = '';
    $confirmedemail = '0';
    $signupmethod = '';
    $recommended_fights_today = '';
}
// get info for fight lists
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ty = $conn->query("SELECT id, listname, stringoffightids FROM lists WHERE showingonindex >= 1 OR id = '1' ORDER BY showingonindex ASC");
    $rt = $ty->fetchall();
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
$rt1 = array_slice($rt, 0, 1);
$rt2 = array_slice($rt, 1, 3);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ty = $conn->query("SELECT id, pic1filepath, promotion, eventname, date FROM fightcards WHERE hasstarted = '1' ORDER BY date DESC LIMIT 20");
    $previousevents = $ty->fetchall();
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-0280357657750466" crossorigin="anonymous"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fighting Tomatoes</title>
    <meta name="description" content="Find Entertaining MMA Fights Based on Fan Ratings and Reviews.">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/modern-business.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick-theme.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta property="fb:app_id" content="337305378235332" />
    <meta property="fb:admins" content="885725289" />
    <meta property="og:title" content="FightingTomatoes | Best UFC & MMA Fights" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="https://fightingtomatoes.com/images/FightingTomatoes-logo-full.jpg" />
    <meta property="og:url" content="https://fightingtomatoes.com/" />
    <meta property="og:description" content="Find Entertaining Fights Based on Fan Ratings and Reviews" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FightingTomatoes | Best UFC & MMA Fights">
    <meta name="twitter:description" content="Find Entertaining Fights Based on Fan Ratings and Reviews">
    <meta name="twitter:image" content="https://fightingtomatoes.com/images/FightingTomatoes-logo-full.jpg">
    <script>
        var fightidarrayjavascript = new Array();
        var firstvotehappenedarrayjavascript = new Array();
        var currentpercentscore = new Array();
        var existingvotejavascript = new Array();
    </script>
</head>
<?php
$otherdbname = "userfightratings";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT popularfreefights, rightimage2path, rightimage2text1, rightimage2text2, rightimage2link, item5type, item5field1, item5field2, item2bannerurl, item2bannerline1text, item2bannerline2text, item2bannerlink, item3bannerurl, item3bannerline1text, item3bannerline2text, item3bannerlink, item4bannerurl, item4bannerline1text, item4bannerline2text, item4bannerlink, item5bannerurl, item5bannerline1text, item5bannerline2text, item5bannerlink, bannerurl, featuredfighterimage, featuredfightermessage, bannerline1text, bannerline2text, bannerlink, featuredfighter, item1type, item1field1, item1field2, item2type, item2field1, item2field2, item3type, item3field1,  item3field2, item4type, item4field1, item4field2, recentlywatchedstring, 	classicsstring, watchableonyoutubestring, r1score, r1comment, r1link, r1linktitle, r1avatar, r1displayname, r1helpful, r1mediaorganization, r1mediaorganizationwebsite, r1fighturl, r1ismedia, r2score, r2comment, r2link, r2linktitle, r2avatar, r2displayname, r2helpful, r2mediaorganization, r2mediaorganizationwebsite, r2fighturl, r2ismedia, r3score, r3comment, r3link, r3linktitle, r3avatar, r3displayname, r3helpful, r3mediaorganization, r3mediaorganizationwebsite, r3fighturl, r3ismedia, f1name, f1nname, f1record, f1avgfightscore, f1pic1filepath, f2name, f2nname, f2record, f2avgfightscore, f2pic1filepath, f3name, f3nname, f3record, f3avgfightscore, f3pic1filepath, f4name, f4nname, f4record, f4avgfightscore, f4pic1filepath, f5name, f5nname, f5record, f5avgfightscore, f5pic1filepath, f6name, f6nname, f6record, f6avgfightscore, f6pic1filepath, f7name, f7nname, f7record, f7avgfightscore, f7pic1filepath, rev1displayname, rev1avatar, rev1mediaorganization, rev1mediaorganizationwebsite, rev1numreviews, rev1reviewerscore, rev2displayname, rev2avatar, rev2mediaorganization, rev2mediaorganizationwebsite, rev2numreviews, rev2reviewerscore, rev3displayname, rev3avatar, rev3mediaorganization, rev3mediaorganizationwebsite, rev3numreviews, rev3reviewerscore, rev4displayname, rev4avatar, rev4mediaorganization, rev4mediaorganizationwebsite, rev4numreviews, rev4reviewerscore, rev5displayname, rev5avatar, rev5mediaorganization, rev5mediaorganizationwebsite, rev5numreviews, rev5reviewerscore, topfightsrecentlylist FROM indexcontent WHERE id = '1'");
    $w = $q->fetchall();
    $bannerurl = $w[0]['bannerurl'];
    $bannerline1 = $w[0]['bannerline1text'];
    $bannerline2 = $w[0]['bannerline2text'];
    $bannerlink = $w[0]['bannerlink'];
    $topfightsrecentlylist = $w[0]['topfightsrecentlylist'];
    $item2bannerurl = $w[0]['item2bannerurl'];
    $item2bannerline1 = $w[0]['item2bannerline1text'];
    $item2bannerline2 = $w[0]['item2bannerline2text'];
    $item2bannerlink = $w[0]['item2bannerlink'];
    $item3bannerurl = $w[0]['item3bannerurl'];
    $item3bannerline1 = $w[0]['item3bannerline1text'];
    $item3bannerline2 = $w[0]['item3bannerline2text'];
    $item3bannerlink = $w[0]['item3bannerlink'];
    $item4bannerurl = $w[0]['item4bannerurl'];
    $item4bannerline1 = $w[0]['item4bannerline1text'];
    $item4bannerline2 = $w[0]['item4bannerline2text'];
    $item4bannerlink = $w[0]['item4bannerlink'];
    $item5bannerurl = $w[0]['item5bannerurl'];
    $item5bannerline1 = $w[0]['item5bannerline1text'];
    $item5bannerline2 = $w[0]['item5bannerline2text'];
    $item5bannerlink = $w[0]['item5bannerlink'];
    $rightimage2path = $w[0]['rightimage2path'];
    $rightimage2text1 = $w[0]['rightimage2text1'];
    $rightimage2text2 = $w[0]['rightimage2text2'];
    $rightimage2link = $w[0]['rightimage2link'];
    $recentlywatchedstring = $w[0]['recentlywatchedstring'];
    $classicsstring = $w[0]['classicsstring'];
    $ytstring = $w[0]['watchableonyoutubestring'];
    $popularfreefights = $w[0]['popularfreefights'];
    $recentarray = explode("-", $recentlywatchedstring);
    $classicsarray = explode("-", $classicsstring);
    $ytarray = explode("-", $ytstring);
    $popularfreefights = explode("-", $popularfreefights);
    $item1type = $w[0]['item1type'];
    $item1field1 = $w[0]['item1field1'];
    $item1field2 = $w[0]['item1field2'];
    $item2type = $w[0]['item2type'];
    $item2field1 = $w[0]['item2field1'];
    $item2field2 = $w[0]['item2field2'];
    $item3type = $w[0]['item3type'];
    $item3field1 = $w[0]['item3field1'];
    $item3field2 = $w[0]['item3field2'];
    $item4type = $w[0]['item4type'];
    $item4field1 = $w[0]['item4field1'];
    $item4field2 = $w[0]['item4field2'];
    $item5type = $w[0]['item5type'];
    $item5field1 = $w[0]['item5field1'];
    $item5field2 = $w[0]['item5field2'];
    $featuredfighter = $w[0]['featuredfighter'];
    $featuredfighterimage = $w[0]['featuredfighterimage'];
    $featuredfightermessage = $w[0]['featuredfightermessage'];
    $ffname = explode(" ", $featuredfighter);
    $rscore[0] = $w[0]['r1score'];
    $rcomment[0] = $w[0]['r1comment'];
    $rlink[0] = $w[0]['r1link'];
    $rlinktitle[0] = $w[0]['r1linktitle'];
    $ravatar[0] = $w[0]['r1avatar'];
    $rdisplayname[0] = $w[0]['r1displayname'];
    $rhelpful[0] = $w[0]['r1helpful'];
    $rmediaorganization[0] = $w[0]['r1mediaorganization'];
    $rmediaorganizationwebsite[0] = $w[0]['r1mediaorganizationwebsite'];
    $rfighturl[0] = $w[0]['r1fighturl'];
    $rismedia[0] = $w[0]['r1ismedia'];
    $rscore[1] = $w[0]['r2score'];
    $rcomment[1] = $w[0]['r2comment'];
    $rlink[1] = $w[0]['r2link'];
    $rlinktitle[1] = $w[0]['r2linktitle'];
    $ravatar[1] = $w[0]['r2avatar'];
    $rdisplayname[1] = $w[0]['r2displayname'];
    $rhelpful[1] = $w[0]['r2helpful'];
    $rmediaorganization[1] = $w[0]['r2mediaorganization'];
    $rmediaorganizationwebsite[1] = $w[0]['r2mediaorganizationwebsite'];
    $rfighturl[1] = $w[0]['r2fighturl'];
    $rismedia[1] = $w[0]['r2ismedia'];
    $rscore[2] = $w[0]['r3score'];
    $rcomment[2] = $w[0]['r3comment'];
    $rlink[2] = $w[0]['r3link'];
    $rlinktitle[2] = $w[0]['r3linktitle'];
    $ravatar[2] = $w[0]['r3avatar'];
    $rdisplayname[2] = $w[0]['r3displayname'];
    $rhelpful[2] = $w[0]['r3helpful'];
    $rmediaorganization[2] = $w[0]['r3mediaorganization'];
    $rmediaorganizationwebsite[2] = $w[0]['r3mediaorganizationwebsite'];
    $rfighturl[2] = $w[0]['r3fighturl'];
    $rismedia[2] = $w[0]['r3ismedia'];
    $ffname[0] = $w[0]['f1name'];
    $ffnname[0] = $w[0]['f1nname'];
    $ffrecord[0] = $w[0]['f1record'];
    $ffavgfightscore[0] = $w[0]['f1avgfightscore'];
    $ffpic1filepath[0] = $w[0]['f1pic1filepath'];
    $ffname[1] = $w[0]['f2name'];
    $ffnname[1] = $w[0]['f2nname'];
    $ffrecord[1] = $w[0]['f2record'];
    $ffavgfightscore[1] = $w[0]['f2avgfightscore'];
    $ffpic1filepath[1] = $w[0]['f2pic1filepath'];
    $ffname[2] = $w[0]['f3name'];
    $ffnname[2] = $w[0]['f3nname'];
    $ffrecord[2] = $w[0]['f3record'];
    $ffavgfightscore[2] = $w[0]['f3avgfightscore'];
    $ffpic1filepath[2] = $w[0]['f3pic1filepath'];
    $ffname[3] = $w[0]['f4name'];
    $ffnname[3] = $w[0]['f4nname'];
    $ffrecord[3] = $w[0]['f4record'];
    $ffavgfightscore[3] = $w[0]['f4avgfightscore'];
    $ffpic1filepath[3] = $w[0]['f4pic1filepath'];
    $ffname[4] = $w[0]['f5name'];
    $ffnname[4] = $w[0]['f5nname'];
    $ffrecord[4] = $w[0]['f5record'];
    $ffavgfightscore[4] = $w[0]['f5avgfightscore'];
    $ffpic1filepath[4] = $w[0]['f5pic1filepath'];
    $ffname[5] = $w[0]['f6name'];
    $ffnname[5] = $w[0]['f6nname'];
    $ffrecord[5] = $w[0]['f6record'];
    $ffavgfightscore[5] = $w[0]['f6avgfightscore'];
    $ffpic1filepath[5] = $w[0]['f6pic1filepath'];
    $ffname[6] = $w[0]['f7name'];
    $ffnname[6] = $w[0]['f7nname'];
    $ffrecord[6] = $w[0]['f7record'];
    $ffavgfightscore[6] = $w[0]['f7avgfightscore'];
    $ffpic1filepath[6] = $w[0]['f7pic1filepath'];
    $revdisplayname[0] = $w[0]['rev1displayname'];
    $revavatar[0] = $w[0]['rev1avatar'];
    $revmediaorganization[0] = $w[0]['rev1mediaorganization'];
    $revmediaorganizationwebsite[0] = $w[0]['rev1mediaorganizationwebsite'];
    $revnumreviews[0] = $w[0]['rev1numreviews'];
    $revreviewerscore[0] = $w[0]['rev1reviewerscore'];
    $revdisplayname[1] = $w[0]['rev2displayname'];
    $revavatar[1] = $w[0]['rev2avatar'];
    $revmediaorganization[1] = $w[0]['rev2mediaorganization'];
    $revmediaorganizationwebsite[1] = $w[0]['rev2mediaorganizationwebsite'];
    $revnumreviews[1] = $w[0]['rev2numreviews'];
    $revreviewerscore[1] = $w[0]['rev2reviewerscore'];
    $revdisplayname[2] = $w[0]['rev3displayname'];
    $revavatar[2] = $w[0]['rev3avatar'];
    $revmediaorganization[2] = $w[0]['rev3mediaorganization'];
    $revmediaorganizationwebsite[2] = $w[0]['rev3mediaorganizationwebsite'];
    $revnumreviews[2] = $w[0]['rev3numreviews'];
    $revreviewerscore[2] = $w[0]['rev3reviewerscore'];
    $revdisplayname[3] = $w[0]['rev4displayname'];
    $revavatar[3] = $w[0]['rev4avatar'];
    $revmediaorganization[3] = $w[0]['rev4mediaorganization'];
    $revmediaorganizationwebsite[3] = $w[0]['rev4mediaorganizationwebsite'];
    $revnumreviews[3] = $w[0]['rev4numreviews'];
    $revreviewerscore[3] = $w[0]['rev4reviewerscore'];
    $revdisplayname[4] = $w[0]['rev5displayname'];
    $revavatar[4] = $w[0]['rev5avatar'];
    $revmediaorganization[4] = $w[0]['rev5mediaorganization'];
    $revmediaorganizationwebsite[3] = $w[0]['rev5mediaorganizationwebsite'];
    $revnumreviews[4] = $w[0]['rev5numreviews'];
    $revreviewerscore[4] = $w[0]['rev5reviewerscore'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT id FROM fightcards WHERE promotion = '$item1field1' AND eventname = '$item1field2'");
    $f = $q->fetchall();
    $eventid = $f[0]['id'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT id FROM fights WHERE ((promotion = '$item1field1' AND eventname = '$item1field2') OR (promotion = '$item2field1' AND eventname = '$item2field2') OR (promotion = '$item3field1' AND eventname = '$item3field2') OR (promotion = '$item4field1' AND eventname = '$item4field2') OR (promotion = '$item5field1' AND eventname = '$item5field2'))  AND deleted != '1' ORDER BY orderoncard DESC");
    $f = $q->fetchall();
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$fightsonpagearray = array();
$justfightids = array();
foreach ($f as $thisfight) {
    array_push($fightsonpagearray, $thisfight['id']);
    array_push($justfightids, $thisfight['id']);
}
foreach ($rt1 as $thislist) {
    $thisstringofids =  $thislist['stringoffightids'];
    $thisarrayofids = explode(',', $thisstringofids);
    foreach ($thisarrayofids as $thisfightid) {
        array_push($justfightids, $thisfightid);
    }
}
$howmanyfights = count($justfightids);
$hasengagedthisfightarray = array();
$userratingarray = array();
foreach ($justfightids as $thisnfi) {
    if ($loggeduser !== 'notloggedin') {      // if user is logged in, get their previous votes on these fights.
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$otherdbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $w = $conn->query("SELECT score, id FROM `$tablename` WHERE fightid = '$thisnfi'");
            $a = $w->fetchall();
            $hasengagedthisfightarray[$thisnfi] = count($a);
            if ($hasengagedthisfightarray[$thisnfi] > 0) {            
                $userratingarray[$thisnfi] = $a[0]['score'];
                if ($userratingarray[$thisnfi] == '11') {
                    $userratingarray[$thisnfi] = 'none';
                }
            } else {
                $userratingarray[$thisnfi] = 'none';
            }
        } catch (PDOException $e) {
        }
        $conn = null;
    } else {
        $userratingarray[$thisnfi] = 'none';
    }
}
foreach ($hasengagedthisfightarray as $key => $val) {
?>
    <script>
  
        fightidarrayjavascript['<?php echo $key ?>'] = '<?php echo $val; ?>';
        firstvotehappenedarrayjavascript['<?php echo $key ?>'] = '<?php echo $val; ?>';
    </script>
<?php
}
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT d1freefighttitle, d1freeimagefilepath, d1freepromotion, d1freeeventname, d1freeurlname, d1classicfighttitle, d1classicimagefilepath, d1classicpromotion, d1classiceventname, d1classicurlname, d1featuredfighter, d1featuredfighterimage FROM dailyfeatures WHERE id = '1'");
    $w = $q->fetchall();
    $d1freefighttitle = $w[0]['d1freefighttitle'];
    $d1freefightbannerimage = $w[0]['d1freeimagefilepath'];
    $d1freepromotion = $w[0]['d1freepromotion'];
    $d1freeeventname = $w[0]['d1freeeventname'];
    $d1freeurlname = $w[0]['d1freeurlname'];
    $d1classicfighttitle = $w[0]['d1classicfighttitle'];
    $d1classicfightbannerimage = $w[0]['d1classicimagefilepath'];
    $d1classicpromotion = $w[0]['d1classicpromotion'];
    $d1classiceventname = $w[0]['d1classiceventname'];
    $d1classicurlname = $w[0]['d1classicurlname'];
    $d1featuredfighter = $w[0]['d1featuredfighter'];
    $d1featuredfighterimage = $w[0]['d1featuredfighterimage'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
$ff3name = explode(" ", $d1featuredfighter, 2);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT percentscore FROM fights WHERE fighttitle = '$d1freefighttitle'");
    $w = $q->fetchall();
    $d1freefightpercentscore = $w[0]['percentscore'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT percentscore FROM fights WHERE fighttitle = '$d1classicfighttitle'");
    $w = $q->fetchall();
    $d1classicfightpercentscore = $w[0]['percentscore'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $conn->query("SELECT avgfightscore FROM fighters WHERE fname = '$ff3name[0]' AND lname = '$ff3name[1]' ");
    $w = $q->fetchall();
    $d1ffavgscore = $w[0]['avgfightscore'];
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ss = $conn->query("SELECT * FROM fightcards WHERE id > $eventid ORDER BY id ASC LIMIT 3"); /* I WILL HAVE TO DYNAMICALLY INSERT ID OF NEXT UPCOMIGN EVENT HERE */
    $vv = $ss->fetchall();
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $qtop10 = $conn->query("SELECT fname, lname, avgfightscore, pic1filepath, numratings, numgreatfights FROM fighters WHERE numratings > 95 ORDER BY avgfightscore DESC LIMIT 10");
    $ftop10 = $qtop10->fetchall();
    $numfighters = count($ftop10);
} catch (PDOException $e) {
    echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
}
$conn = null;
?>
<body style="background-color:#181818;">
    <?php include_once("analyticstracking.php"); ?>
    <!-- Navigation -->
    <?php
    ?>
    <script>
        window.twttr = (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0],
                t = window.twttr || {};
            if (d.getElementById(id)) return t;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);
            t._e = [];
            t.ready = function(f) {
                t._e.push(f);
            };
            return t;
        }(document, "script", "twitter-wjs"));
    </script>
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '337305378235332',
                xfbml: true,
                version: 'v2.7'
            });
        };
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        function myFacebookLogin() {
            FB.login(
                function(response) {
                    if (response.authResponse) {
                        console.log('Welcome!  Fetching your information.... ');
                        FB.api('/me', {
                            fields: 'name,email'
                        }, function(response) {
                            if (response.email) {
                              
                            } else {
                              	
                                alert('fightingtomatoes.com needs permission to know your email in order to sign you up using Facebook. Please try again.');
                                return;
                            }
                            document.getElementById('fbemail').value = response.email;
 
                            document.getElementById("signingupusingfacebook").submit();
                        });
                    } else {
                        //console.log('User cancelled login or did not fully authorize.');
                    }
                }, {
                    'scope': 'email',
                    auth_type: 'rerequest'
                });
        }
    </script>
    <form id="signingupusingfacebook" style="display:none;" action="signingupusingfacebook.php" method="POST">
        <input type="checkbox" style="margin-left:10px; display:none;" name="keepmeloggedinfb" id="keepmeloggedinfb" value="0"><!-- &nbsp Keep Me Logged In --></input>
        <input type="hidden" name="fbemail" id="fbemail" value="" />
    </form>
    <?php
    //}
    ?>
    <?php
    include 'header.php';
    ?>
    <!-- Page Content -->
    <div class="container" style="margin-top:0px;">
        <div class="desktop-display">
            <div class="row"> <!-- beginning of row for slider -->
                <div class="col-lg-12"> <!-- beginning of col for slider -->
                    <?php
                    $namecount = 0;
                    foreach ($rt1 as $thislist) {
                        $arrayoffights = array();
                        $thislistname = $thislist['listname'];
                        $thislistid = $thislist['id'];
                        $thisliststringoffightids = $thislist['stringoffightids'];
                   
                        $arrayoffights = explode(",", $thisliststringoffightids);
                    ?>
                        <div class="wrapper" style="margin-top:50px;">
                            <h2 class="scrollerTitle" style="color:white; font-size:20px; font-weight:900; margin-left:0px;"><?php echo $thislistname ?><h2>
                                    <div class="carousel">
                                        <?php
                                        foreach ($arrayoffights as $thisfight) {
                                            $hasengagedthisfight = '';
                                            $fightid = $thisfight;
                                            if ($loggeduser !== 'notloggedin') {  
                                                try {
                                                    $conn = new PDO("mysql:host=$servername;dbname=$otherdbname", $username, $password);
                                                    
                                                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                    $thisnfi = $thisfight;
                                                    $w = $conn->query("SELECT score, id, excited FROM `$tablename` WHERE fightid = '$thisnfi'");
                                                    $a = $w->fetchall();
                                                    $hasengagedthisfight = count($a);
                                            
                                                    if ($hasengagedthisfight > 0) {        
                                                        $ratingtoshow = $a[0]['score'];
                                                        $userexcited = $a[0]['excited'];
                                                        if ($ratingtoshow == '11') {
                                                       
                                                            $ratingtoshow = '-';
                                                        }
                                                    } else {
                                                        $hasengagedthisfight = 0;
                                                        $ratingtoshow = '-';
                                                        $userexcited = '0';
                                                    }
                                                } catch (PDOException $e) {
                                                    
                                                }
                                                $conn = null;
                                            } else {
                                                $ratingtoshow = '-';
                                                $userexcited = '0';
                                            } 
                                            try {
                                                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                                  
                                                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                $www = $conn->query("SELECT promotion, eventname, date, percentscore, fightbannerimage, f1fn, f1ln, f2fn, f2ln, rematch, numvotes, numreviews, urlname, thumbnail_filepath FROM fights WHERE id = '$thisfight'");
                                                $aaa = $www->fetchall();
                                            } catch (PDOException $e) {
                                       
                                            }
                                            $conn = null;
                                            $fighttitle = addslashes($aaa[0]['f1fn']) . " " . addslashes($aaa[0]['f1ln']) . " vs. " . addslashes($aaa[0]['f2fn']) . " " . addslashes($aaa[0]['f2ln']);
                                            $thisrematch = $aaa[0]['rematch'];
                                            $numreviews = $aaa[0]['numreviews'];
                                            $numvotes = $aaa[0]['numvotes'];
                                            $percentscore = $aaa[0]['percentscore'];
                                            $thumbnail_filepath = $aaa[0]['thumbnail_filepath'];
                                            $oldnumvotes = 'oldnumvotes' . $thisfight;
                                            $oldpercentscore = 'oldpercentscore' . $thisfight;
                                            $olduserrating = 'olduserrating' . $thisfight;
                                        ?>
                                            <!-- this next section differentiates between mobile and desktop, showing differently on each -->
                                            <div style="padding:3px;">
                                                <div style="background-color:#202020; border-radius:4px; text-align:left; height:300px;">
                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                        <img src="<?php echo $thumbnail_filepath ?>" style="margin-bottom:0px;">
                                                    </a>
                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                        <div class="sliderFightTitle" style="padding:15px; padding-top:0px; margin-bottom:15px; margin-top:11px;">
                                                            <div class="underline-on-hover" style="padding-bottom:0px; font-weight:700;">
                                                                <?php
                                                                echo $aaa[0]['f1fn'] . " " . $aaa[0]['f1ln'] . " vs. " . $aaa[0]['f2fn'] . " " . $aaa[0]['f2ln'];
                                                                if ($thisrematch > 1) {
                                                                    echo " $thisrematch";
                                                                }
                                                                ?>
                                                            </div>
                                                            <div style="margin-top:5px;">
                                                                <span class="sliderFightEvent"><?php echo $aaa[0]['promotion'] ?> <?php echo $aaa[0]['eventname'] ?></span>
                                                                <?php
                                                                $date = date_create($aaa[0]['date']);
                                                                ?>
                                                                <span class="sliderFightEvent"><?php echo date_format($date, "F jS Y"); ?> </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <input type="hidden" id="<?php echo $oldnumvotes ?>" name="<?php echo $oldnumvotes ?>" value="<?php echo $numvotes ?>" />
                                                    <input type="hidden" id="<?php echo $oldpercentscore ?>" name="<?php echo $oldpercentscore ?>" value="<?php echo $percentscore ?>" />
                                                    <input type="hidden" id="<?php echo $olduserrating ?>" name="<?php echo $olduserrating ?>" value="<?php echo $ratingtoshow ?>" />
                                                    <div style="margin-top:-20px; padding:0px 10px 0px 15px;">
                                                        <div class="row">
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <div class="col-lg-6">
                                                                    <?php
                                                                    $thisyellowstarimg = "yellowstarimg" . $thislistid . "-" . $fightid;
                                                                    $thispercentscorespan = "percentscorespan" . $thislistid . "-" . $fightid;
                                                                    $thisratingspan = "ratingspan" . $thislistid . "-" . $fightid;
                                                                    if ($aaa[0]['percentscore'] >= 85) {
                                                                    ?>
                                                                        
                                                                        <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star-tomatoes.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                    <?php
                                                                    } elseif ($aaa[0]['percentscore'] >= 70) {
                                                                    ?>
                                                                        <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/empty-star.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </a>
                                                            <div class="col-lg-6" style="overflow:visible !important;">
                 
                                                                <?php
                                                                $thisscorename = "score" . $thislistid . "-" . $fightid;
                                                                $thisbluestarimg = "bluestarimg" . $thislistid . "-" . $fightid;
                                                                $thisbluestarbutton = "bluestarbutton" . $thislistid . "-" . $fightid;
                                                                if (($confirmedemail == '0') && ($signupmethod == 'email')) {
                                                                ?>
                                                                    <button data-toggle="modal" data-target="#myModalconfirmemail" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype" style="height:24px; width:24px;  display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"></span></button>
                                                                    <?php
                                                                } else {
                                                                    if ($hasengagedthisfight > 0) {       // if has voted yet.
                                                                    ?>
                                                                        <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star-full.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-thumbnailtype" style="height:24px; width:24px; display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"><?php echo $ratingtoshow ?></span></button>
                                                                    <?php
                                                                    } else {      // has not voted on this fight yet.
                                                                    ?>
                                                                        <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype" style="height:24px; width:24px; display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"></span></button>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                        </div>
                    <?php
                    }
                    ?>
                    <br>
                </div> <!--  end of col for slider -->
            </div> <!-- end of row for sliders -->
        </div> <!-- end of desktop sliders -->
        <div class="mobile-display">
            <div class="row"> <!-- beginning of row for slider -->
                <div class="col-lg-12"> <!-- beginning of col for slider -->
                    <?php
                    $namecount = 0;
                    foreach ($rt1 as $thislist) {
                        $arrayoffights = array();
                        $thislistname = $thislist['listname'];
                        $thislistid = $thislist['id'];
                        $thisliststringoffightids = $thislist['stringoffightids'];
                        $arrayoffights = explode(",", $thisliststringoffightids);
                    ?>
                        <div class="wrapper" style="margin-top:10px;">
                            <h2 class="scrollerTitle" style="color:white; font-size:20px; font-weight:900; margin-left:0px;"><?php echo $thislistname ?><h2>
                                    <div class="carousel">
                                        <?php
                                        foreach ($arrayoffights as $thisfight) {
                                            $hasengagedthisfight = '';
                                            $fightid = $thisfight;
                                            if ($loggeduser !== 'notloggedin') {    
                                                try {
                                                    $conn = new PDO("mysql:host=$servername;dbname=$otherdbname", $username, $password);
                                                
                                                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                
                                                    $thisnfi = $thisfight;
                                                    $w = $conn->query("SELECT score, id, excited FROM `$tablename` WHERE fightid = '$thisnfi'");
                                                    $a = $w->fetchall();
                                                    $hasengagedthisfight = count($a);
                                                    if ($hasengagedthisfight > 0) {            /// if already voted then just change vote
                                                        $ratingtoshow = $a[0]['score'];
                                                        $userexcited = $a[0]['excited'];
                                                        if ($ratingtoshow == '11') {
                                                            //$ratingtoshow = 'none';	
                                                            $ratingtoshow = '-';
                                                        }
                                                    } else {
                                                        $hasengagedthisfight = 0;
                                                        $ratingtoshow = '-';
                                                        $userexcited = '0';
                                                    }
                                                } catch (PDOException $e) {
                                                    //echo "<br>" . $e->getMessage();
                                                }
                                                $conn = null;
                                            } else {
                                           
                                                $ratingtoshow = '-';
                                                $userexcited = '0';
                                            } // end of if user is logged in, set variables to show
                                            try {
                                                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                        
                                                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                $www = $conn->query("SELECT promotion, eventname, date, percentscore, fightbannerimage, f1fn, f1ln, f2fn, f2ln, rematch, numvotes, numreviews, urlname, thumbnail_filepath FROM fights WHERE id = '$thisfight'");
                                                $aaa = $www->fetchall();
                                            } catch (PDOException $e) {
                                           
                                            }
                                            $conn = null;
                                            $fighttitle = addslashes($aaa[0]['f1fn']) . " " . addslashes($aaa[0]['f1ln']) . " vs. " . addslashes($aaa[0]['f2fn']) . " " . addslashes($aaa[0]['f2ln']);
                                            $thisrematch = $aaa[0]['rematch'];
                                            $numreviews = $aaa[0]['numreviews'];
                                            $numvotes = $aaa[0]['numvotes'];
                                            $percentscore = $aaa[0]['percentscore'];
                                            $thumbnail_filepath = $aaa[0]['thumbnail_filepath'];
                                            $oldnumvotes = 'oldnumvotes' . $thisfight;
                                            $oldpercentscore = 'oldpercentscore' . $thisfight;
                                            $olduserrating = 'olduserrating' . $thisfight;
                     
                                        ?>
                                            <div style="padding:3px;">
                                                <div style="background-color:#202020; border-radius:4px; text-align:left; height:300px;">
                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                        <img src="<?php echo $thumbnail_filepath ?>" style="margin-bottom:0px;">
                                                    </a>
                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                        <div class="sliderFightTitle" style="padding:0px; margin-bottom:15px; margin-top:11px;">
                                                            <div class="underline-on-hover" style="padding-bottom:10px; font-weight:700;">
                                                                <?php
                                                                echo $aaa[0]['f1fn'] . " " . $aaa[0]['f1ln'] . " vs. " . $aaa[0]['f2fn'] . " " . $aaa[0]['f2ln'];
                                                                if ($thisrematch > 1) {
                                                                    echo " $thisrematch";
                                                                }
                                                                ?>
                                                            </div>
                                                            <span class="sliderFightEvent"><?php echo $aaa[0]['promotion'] ?> <?php echo $aaa[0]['eventname'] ?></span>
                                                            <br>
                                                            
                                                            <?php
                                                            $date = date_create($aaa[0]['date']);
                                                            ?>
                                                            <span class="sliderFightEvent"><?php echo date_format($date, "F jS Y"); ?> </span>
                                                        </div>
                                                    </a>
                                                    <input type="hidden" id="<?php echo $oldnumvotes ?>" name="<?php echo $oldnumvotes ?>" value="<?php echo $numvotes ?>" />
                                                    <input type="hidden" id="<?php echo $oldpercentscore ?>" name="<?php echo $oldpercentscore ?>" value="<?php echo $percentscore ?>" />
                                                    <input type="hidden" id="<?php echo $olduserrating ?>" name="<?php echo $olduserrating ?>" value="<?php echo $ratingtoshow ?>" />
                                                    <div class="row" style="width:100%; margin-left:0px;">
                                                        <div class="col-xs-6" style="margin:0px; padding:0px;">
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <?php
                                                                $thisyellowstarimg = "yellowstarimg-mobile" . $thislistid . "-" . $fightid;
                                                                $thispercentscorespan = "percentscorespan-mobile" . $thislistid . "-" . $fightid;
                                                                $thisratingspan = "ratingspan-mobile" . $thislistid . "-" . $fightid;
                                                                if ($aaa[0]['percentscore'] >= 85) {
                                                                ?>
                                                                    <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                        <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star-tomatoes.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                        <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                    </div>
                                                                <?php
                                                                } elseif ($aaa[0]['percentscore'] >= 70) {
                                                                ?>
                                                                    <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                        <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                        <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                    </div>
                                                                <?php
                                                                } else {
                                                                ?>
                                                                    <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                        <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/empty-star.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                        <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-xs-6" style="margin:0px; padding:0px;">
                                                            <?php
                                                       
                                                            $thisscorename = "score-mobile" . $thislistid . "-" . $fightid;
                                                            $thisbluestarimg = "bluestarimg-mobile" . $thislistid . "-" . $fightid;
                                                            $thisbluestarbutton = "bluestarbutton-mobile" . $thislistid . "-" . $fightid;
                                                            if (($confirmedemail == '0') && ($signupmethod == 'email')) {
                                                            ?>
                                                                <div style="width:100%; margin:0px; padding:0px; ">
                                                                    <button data-toggle="modal" data-target="#myModalconfirmemail" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype-mobile">
                                                                        <img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                        <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"></span>
                                                                    </button>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                if ($hasengagedthisfight > 0) {       // if has voted yet.
                                                                ?>
                                                                    <div style="width:100%; margin:0px; padding:0px; ">
                                                                        <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-thumbnailtype-mobile">
                                                                            <img src="https://fightingtomatoes.com/blue-star-full.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                            <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"><?php echo $ratingtoshow ?></span>
                                                                        </button>
                                                                    </div>
                                                                <?php
                                                                } else {      // has not voted on this fight yet.
                                                                ?>
                                                                    <div style="width:100%; margin:0px; padding:0px; ">
                                                                        <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype-mobile">
                                                                            <img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                            <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"></span>
                                                                        </button>
                                                                    </div>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                        </div>
                    <?php
                    }
                    ?>
                    <br>
                </div> <!--  end of col for slider -->
            </div> <!-- end of row for sliders -->
        </div> <!-- end of mobile sliders -->
        <div class="row" style="padding-bottom:0px; margin-bottom:0px;"> <!-- beginning of row for events slider -->
            <div class="col-lg-12"> <!-- beginning of col for events slider -->
                <div class="wrapper" style="margin-top:0px;">
                    <h2 class="scrollerTitle" style="color:white; font-size:20px; font-weight:900; margin-left:0px;">RECENT EVENTS<h2>
                            <div class="carouselCustom">
                                <?php
                                $todaysdate = date('Y-m-d H:i:s');
                                $todaysdate = strtotime($todaysdate);
                                foreach ($previousevents as $thispreviousevent) {
                                    $eventimage = $thispreviousevent['pic1filepath'];
                                    $eventeventname = $thispreviousevent['eventname'];
                                    $eventpromotion = $thispreviousevent['promotion'];
                                    $eventdate = $thispreviousevent['date'];
                                    $utime = strtotime($eventdate);
                                    $thisdatetoshow = date('F jS Y', $utime); 
                                ?>
                                    <div style="padding:3px;">
                                        <div style="background-color:#181818; border-radius:4px; text-align:left;">
                                            <a href="https://fightingtomatoes.com/event/<?php echo $eventpromotion ?>/<?php echo $eventeventname ?>" style="text-decoration:none; outline: none;">
                                                <img src="<?php echo $eventimage ?>" style="margin-bottom:0px; width:100%; padding-bottom:0px;">
                                                <div style="color:white; font-size:14px; font-weight:700; margin-top:10px;">
                                                    <?php echo $eventpromotion . " " . $eventeventname ?>
                                                </div>
                                                <div style="color:#666666; font-size:12px; font-weight:500;  margin-top:10px;">
                                                    <?php echo $thisdatetoshow ?>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                </div>
                <br>
            </div> <!--  end of col for event slider -->
        </div> <!-- end of row for event sliders -->
        <!-- BANNER -->
        <div class="row" style="background-color:none; padding:0px; padding-left:0px; padding-top:40px;">
            <div class="col-lg-8" style=" line-height:26px; position:relative; padding-bottom:13px;">
                <input type="hidden" id="eventid" name="eventid" value="<?php echo $eventid ?>" />
                <a href="<?php echo $bannerlink ?>">
                    <div style="position:relative;">
                        <img class="img-responsive" src="<?php echo $bannerurl ?>" style="object-fit: cover; border-radius:10px;" alt="<?php echo $bannerline1 ?>" title="<?php echo $bannerline1 ?>" />
                    </div>
                </a>
                <br>
                <div id="item1div">
                    <?php
                    $upcoming = 'current';
                    $itemtype = $item1type;
                    $itemfield1 = $item1field1;
                    $itemfield2 = $item1field2;
                    if (isset($item1listname)) {
                        $itemlistname = $item1listname;
                    }
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
       
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                     
                        $q = $conn->query("SELECT id, mainchannel, mainlink, maintime, prelimchannel, prelimlink, prelimtime FROM fightcards WHERE promotion = '$item1field1' AND eventname = '$item1field2'");
                        $tt = $q->fetchall();
                        $eventid = $tt[0]['id'];
                        $maintime = $tt[0]['maintime'];
                        $mainchannel = $tt[0]['mainchannel'];
                        $mainlink = $tt[0]['mainlink'];
                        $prelimtime = $tt[0]['prelimtime'];
                        $prelimchannel = $tt[0]['prelimchannel'];
                        $prelimlink = $tt[0]['prelimlink'];
                    } catch (PDOException $e) {
                        echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
                    }
                    include 'index-widget-display-NEW-6.php';
                    ?>
                </div>
                <br>
                <br><br>
                <?php
                if ((isset($item2bannerurl)) && ($item2bannerurl != '')) {
                ?>
                    <a href="<?php echo $item2bannerlink ?>">
                        <img class="img-responsive" src="<?php echo $item2bannerurl ?>" style="object-fit: cover; border-radius:10px;" alt="">
                    </a>
                    <br>
                <?php
                }
                ?>
                <div id="item2div">
                    <?php
                    $upcoming = 'past';
                    $itemtype = $item2type;
                    $itemfield1 = $item2field1;
                    $itemfield2 = $item2field2;
                    if (isset($item2listname)) {
                        $itemlistname = $item2listname;
                    }
                    include 'index-widget-display-NEW-6.php';
                    ?>
                </div>
                <br><br><br>
                <div class="row">
                    <div class="col-12" style="text-align:center;">
                        <a href="events.php"><button class="paginationbutton">More Events</button></a>
                    </div>
                </div>
                <br><br>
            </div> <!-- END OF LEFT COLUMN 1 -->
            <div class="col-lg-4" style="background-color:none; line-height:26px; position:relative; padding-bottom:13px; color:white;"> <!-- START OF RIGHT COLUMN 1 -->
                <?php
                foreach ($vv as $thisUpcomingEvent) {
                    $nexteventname = $thisUpcomingEvent['eventname'];
                    $nextpromotion = $thisUpcomingEvent['promotion'];
                    $eventimage = $thisUpcomingEvent['pic1filepath'];
                    $nextdate = $thisUpcomingEvent['date'];
                    $utime = strtotime($nextdate); 
                    $nextdisplaydate = date('D. M. j', $utime); 
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                        
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $nextcardquery = $conn->query("SELECT * FROM fights WHERE promotion = '$nextpromotion' AND eventname = '$nexteventname' AND prelimcode = 'Main' AND deleted != '1' ORDER BY orderoncard DESC");
                        $nextcardfights = $nextcardquery->fetchall();
                        $howmanyfightsonnextcard = count($nextcardfights);
                    } catch (PDOException $e) {
                        echo "<br><br> SQL ATTEMPT: " . $sql . "<br><br>FAILED ERROR:" . $e->getMessage() . "<br><br>";
                    }
                    $conn = null;
                ?>
  
                <?php
                } // end of for each upcoming event
                ?>
                <div style="background-color:#B20000; width:100%; padding:15px; padding-top:20px; padding-bottom:20px; color:white; line-height:20px; border-radius:7px;">
               
                    <img src="images/hellofightfan.png" style="margin-left:-2px; height:20px;" />
                    <br><br>
                    Welcome to Fighting Tomatoes. Our goal is to help you find entertaining fights based on fan reviews. As modern day fight fans, we live in a time with unprecedented access to fight videos online. Fighting Tomatoes is here to help you find and watch the best.
                    <br><br>
                    Touch Gloves,
                    <br><br>
                
                    <img src="images/FOTN-white-glove-22.png" style="margin-top:-8px; margin-right:8px;" />
                    <img src="images/fightingtomatoesstraight.png" style="margin-top:-2px; margin-left:8px;  height:20px;" />
                    <br>
                </div>
                <br><br>
                <div style="padding-bottom:30px; border-bottom:2px solid white; font-weight:900; color:#ffffff; font-size:20px; line-height:38px;">MOST ENTERTAINING FIGHTERS</div>
                <br>
                <div style="width:100%;">
     
                    <?php
                    for ($x = 0; $x < $numfighters; $x++) {
                    ?>
                        <div class="col-12" style=" line-height:26px;">
                            <a href="fighter/<?php echo $ftop10[$x]['fname']; ?>-<?php echo $ftop10[$x]['lname']; ?>">
                                <div style="padding:5px 10px 5px 10px;  height:65px; background-color:#333333; border-radius:5px; margin-bottom:7px;">
                                    <div class="row" style="background-color:none;">
                                        <div class="col-xs-3" style="background-color:none; color:white; font-size:12px;">
                                            <img class="fighterimage-sidebar" src="<?php echo $ftop10[$x]['pic1filepath'] ?>" style="width:95px; margin-top:-2px;" />
                                        </div>
                                        
                                        <div class="col-xs-6" style="background-color:none; color:white; font-size:16px; padding-top:16px; white-space: nowrap;">
                                            <?php
                                            $numcharsf1 = strlen($ftop10[$x]['fname']) + strlen($ftop10[$x]['lname']);
                                            if ($numcharsf1 >= 18) {
                                            ?>
                                                <span style="font-size:14px;">
                                                <?php
                                            } else {
                                                ?>
                                                    <span style="font-size:16px;">
                                                    <?php
                                                }
                                                    ?>
                                                    <?php echo $ftop10[$x]['fname'] . " " . $ftop10[$x]['lname']; ?>
                                                    </span>
                                        </div>
                                        <div class="col-xs-3" style="background-color:none; color:white; font-size:20px; font-weight:900;">
                                            <div style="display: flex; align-items:center; padding-top:15px; margin-left:-21px;">
                                                <?php
                                                if ($ftop10[$x]['avgfightscore'] >= 85) {
                                                ?>
                                                    <img src="https://fightingtomatoes.com/full-star-tomatoes.png" style="height:26px; display:inline-block;" />
                                                <?php
                                                } elseif ($ftop10[$x]['avgfightscore'] >= 70) {
                                                ?>
                                                    <img src="https://fightingtomatoes.com/full-star.png" style="height:26px; display:inline-block;" />
                                                <?php
                                                } else {
                                                ?>
                                                    <img src="https://fightingtomatoes.com/empty-star.png" style="height:26px; display:inline-block;" />
                                                <?php
                                                }
                                                ?>
                                                <span style="color:white; font-size:20px; font-weight:900; margin-left:11px;"><?php echo $ftop10[$x]['avgfightscore'];  ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                    <br><br>
                    <div style="text-align:center;">
                        <a href="fighters.php"><button class="paginationbutton">More Fighters</button></a>
                    </div>
                    <!-- end test code -->
                </div>
                <br><br>
                <div style="padding-bottom:30px; border-bottom:2px solid white; font-weight:900; color:#ffffff; font-size:20px; ">FRIENDS</div>
                <br>
                <a href="https://www.reddit.com/r/MMA/" target="_blank" style="color:white; font-size:16px; text-decoration:none;">
                    <div style="width:100%;">
                        <div style="width:25%; display:inline-block;">
                            <img src="https://fightingtomatoes.com/images/rmmalogo.png" style="width:100%" />
                        </div>
                        <div style="width:55%; display:inline-block;">
                            <b>&nbsp &nbsp r/mma</b>
                        </div>
                    </div>
                </a>
                <br><br>
                <br><br>
            </div> <!-- END OF RIGHT COLUMN 1 -->
        </div> <!-- END OF FIRST ROW / TOP ROW -->
        <div class="row"> 
            <div class="col-lg-12">
                <div class="desktop-display">
                    <div class="row"> <!-- beginning of row for slider -->
                        <div class="col-lg-12"> <!-- beginning of col for slider -->
                            <?php
                            foreach ($rt2 as $thislist) {
                                $arrayoffights = array();
                                $thislistname = $thislist['listname'];
                                $thislistid = $thislist['id'];
                                $thisliststringoffightids = $thislist['stringoffightids'];
                      
                                $arrayoffights = explode(",", $thisliststringoffightids);
                            ?>
                                <div class="wrapper" style="margin-top:50px;">
                                    <h2 class="scrollerTitle" style="color:white; font-size:20px; font-weight:900;"><?php echo $thislistname ?><h2>
                                            <div class="carousel">
                                                <?php
                                                foreach ($arrayoffights as $thisfight) {
                                                    $hasengagedthisfight = '';
                                                    $fightid = $thisfight;
                                                    if ($loggeduser !== 'notloggedin') {      // if user is logged in, set variables to show
                                                        try {
                                                            $conn = new PDO("mysql:host=$servername;dbname=$otherdbname", $username, $password);
                                                            
                                                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                          
                                                            $thisnfi = $thisfight;
                                                            $w = $conn->query("SELECT score, id, excited FROM `$tablename` WHERE fightid = '$thisnfi'");
                                                            $a = $w->fetchall();
                                                            $hasengagedthisfight = count($a);
                                                       
                                                            if ($hasengagedthisfight > 0) {            /// if already voted then just change vote
                                                                $ratingtoshow = $a[0]['score'];
                                                                $userexcited = $a[0]['excited'];
                                                                if ($ratingtoshow == '11') {
                                                                    
                                                                    $ratingtoshow = '-';
                                                                }
                                                            } else {
                                                                $hasengagedthisfight = 0;
                                                                $ratingtoshow = '-';
                                                                $userexcited = '0';
                                                            }
                                                        } catch (PDOException $e) {
                                                           
                                                        }
                                                        $conn = null;
                                                    } else {
                                           
                                                        $ratingtoshow = '-';
                                                        $userexcited = '0';
                                                    } // end of if user is logged in, set variables to show
                                                    try {
                                                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                                               
                                                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                        $www = $conn->query("SELECT promotion, eventname, date, percentscore, fightbannerimage, f1fn, f1ln, f2fn, f2ln, rematch, numvotes, numreviews, urlname, thumbnail_filepath FROM fights WHERE id = '$thisfight'");
                                                        $aaa = $www->fetchall();
                                                    } catch (PDOException $e) {
                                                  
                                                    }
                                                    $conn = null;
                                                    $fighttitle = addslashes($aaa[0]['f1fn']) . " " . addslashes($aaa[0]['f1ln']) . " vs. " . addslashes($aaa[0]['f2fn']) . " " . addslashes($aaa[0]['f2ln']);
                                                    $thisrematch = $aaa[0]['rematch'];
                                                    $numreviews = $aaa[0]['numreviews'];
                                                    $numvotes = $aaa[0]['numvotes'];
                                                    $percentscore = $aaa[0]['percentscore'];
                                                    $thumbnail_filepath = $aaa[0]['thumbnail_filepath'];
                                                    $oldnumvotes = 'oldnumvotes' . $thisfight;
                                                    $oldpercentscore = 'oldpercentscore' . $thisfight;
                                                    $olduserrating = 'olduserrating' . $thisfight;
                                                ?>
                                               
                                                    <div style="padding:3px;">
                                                        <div style="background-color:#202020; border-radius:4px; text-align:left; height:300px;">
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <img src="<?php echo $thumbnail_filepath ?>" style="margin-bottom:0px;">
                                                            </a>
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <div class="sliderFightTitle" style="padding:15px; padding-top:0px; margin-bottom:15px; margin-top:11px;">
                                                                    <div class="underline-on-hover" style="padding-bottom:0px; font-weight:700;">
                                                                        <?php
                                                                        echo $aaa[0]['f1fn'] . " " . $aaa[0]['f1ln'] . " vs. " . $aaa[0]['f2fn'] . " " . $aaa[0]['f2ln'];
                                                                        if ($thisrematch > 1) {
                                                                            echo " $thisrematch";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div style="margin-top:5px;">
                                                                        <span class="sliderFightEvent"><?php echo $aaa[0]['promotion'] ?> <?php echo $aaa[0]['eventname'] ?></span>
                                                                        <?php
                                                                        $date = date_create($aaa[0]['date']);
                                                                        ?>
                                                                        <span class="sliderFightEvent"><?php echo date_format($date, "F jS Y"); ?> </span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <input type="hidden" id="<?php echo $oldnumvotes ?>" name="<?php echo $oldnumvotes ?>" value="<?php echo $numvotes ?>" />
                                                            <input type="hidden" id="<?php echo $oldpercentscore ?>" name="<?php echo $oldpercentscore ?>" value="<?php echo $percentscore ?>" />
                                                            <input type="hidden" id="<?php echo $olduserrating ?>" name="<?php echo $olduserrating ?>" value="<?php echo $ratingtoshow ?>" />
                                                            <div style="margin-top:-20px; padding:0px 10px 0px 15px;">
                                                                <div class="row">
                                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                        <div class="col-lg-6">
                                                                            <?php
                                                                            $thisyellowstarimg = "yellowstarimg" . $thislistid . "-" . $fightid;
                                                                            $thispercentscorespan = "percentscorespan" . $thislistid . "-" . $fightid;
                                                                            $thisratingspan = "ratingspan" . $thislistid . "-" . $fightid;
                                                                            if ($aaa[0]['percentscore'] >= 85) {
                                                                            ?>
                                         
                                                                                <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star-tomatoes.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                            <?php
                                                                            } elseif ($aaa[0]['percentscore'] >= 70) {
                                                                            ?>
                                                                                <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                            <?php
                                                                            } else {
                                                                            ?>
                                                                                <div title="Overall Score" class="yellowstarcontainer-thumbnailtype"><img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/empty-star.png" class="yellowstar-thumbnailtype" /><br class="mobile-break"><span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span> <span class="numvotesspan" style="display:inline; width:20px; position:relative; color:#999999; font-weight:500;"></span> </div>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </a>
                                                                    <div class="col-lg-6" style="overflow:visible !important;">
                                                                      
                                                                        <?php
                                                                        $thisscorename = "score" . $thislistid . "-" . $fightid;
                                                                        $thisbluestarimg = "bluestarimg" . $thislistid . "-" . $fightid;
                                                                        $thisbluestarbutton = "bluestarbutton" . $thislistid . "-" . $fightid;
                                                                        if (($confirmedemail == '0') && ($signupmethod == 'email')) {
                                                                        ?>
                                                                            <button data-toggle="modal" data-target="#myModalconfirmemail" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype" style="height:24px; width:24px;  display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"></span></button>
                                                                            <?php
                                                                        } else {
                                                                            if ($hasengagedthisfight > 0) {       // if has voted yet.
                                                                            ?>
                                                                                <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star-full.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-thumbnailtype" style="height:24px; width:24px; display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"><?php echo $ratingtoshow ?></span></button>
                                                                            <?php
                                                                            } else {      // has not voted on this fight yet.
                                                                            ?>
                                                                                <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'desktop', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype"><img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype" style="height:24px; width:24px; display:inline-block;" /><span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" style="margin-left:10px; font-weight:900; font-size:16px; color:#83B4F3;"></span></button>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                </div>
                            <?php
                            }
                            ?>
                            <br>
                        </div> <!--  end of col for slider -->
                    </div> <!-- end of row for sliders -->
                </div> <!-- end of desktop sliders -->
                <div class="mobile-display">
                    <div class="row"> <!-- beginning of row for slider -->
                        <div class="col-lg-12"> <!-- beginning of col for slider -->
                            <?php
                            foreach ($rt2 as $thislist) {
                                $arrayoffights = array();
                                $thislistname = $thislist['listname'];
                                $thislistid = $thislist['id'];
                                $thisliststringoffightids = $thislist['stringoffightids'];
             
                                $arrayoffights = explode(",", $thisliststringoffightids);
                            ?>
                                <div class="wrapper" style="margin-top:10px;">
                                    <h2 class="scrollerTitle" style="color:white; font-size:20px; font-weight:900; margin-left:0px;"><?php echo $thislistname ?><h2>
                                            <div class="carousel">
                                                <?php
                                                foreach ($arrayoffights as $thisfight) {
                                                    $hasengagedthisfight = '';
                                                    $fightid = $thisfight;
                                                    if ($loggeduser !== 'notloggedin') {      // if user is logged in, set variables to show
                                                        try {
                                                            $conn = new PDO("mysql:host=$servername;dbname=$otherdbname", $username, $password);
                                                         
                                                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                            $thisnfi = $thisfight;
                                                            $w = $conn->query("SELECT score, id, excited FROM `$tablename` WHERE fightid = '$thisnfi'");
                                                            $a = $w->fetchall();
                                                            $hasengagedthisfight = count($a);
                                                            if ($hasengagedthisfight > 0) {            
                                                                $ratingtoshow = $a[0]['score'];
                                                                $userexcited = $a[0]['excited'];
                                                                if ($ratingtoshow == '11') {
                                                                  
                                                                    $ratingtoshow = '-';
                                                                }
                                                            } else {
                                                                $hasengagedthisfight = 0;
                                                                $ratingtoshow = '-';
                                                                $userexcited = '0';
                                                            }
                                                        } catch (PDOException $e) {
                                                            
                                                        }
                                                        $conn = null;
                                                    } else {
                                                       
                                                        $ratingtoshow = '-';
                                                        $userexcited = '0';
                                                    } // end of if user is logged in, set variables to show
                                                    try {
                                                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                                                       
                                                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                        $www = $conn->query("SELECT promotion, eventname, date, percentscore, fightbannerimage, f1fn, f1ln, f2fn, f2ln, rematch, numvotes, numreviews, urlname, thumbnail_filepath FROM fights WHERE id = '$thisfight'");
                                                        $aaa = $www->fetchall();
                                                    } catch (PDOException $e) {
                                                       
                                                    }
                                                    $conn = null;
                                                    $fighttitle = addslashes($aaa[0]['f1fn']) . " " . addslashes($aaa[0]['f1ln']) . " vs. " . addslashes($aaa[0]['f2fn']) . " " . addslashes($aaa[0]['f2ln']);
                                                    $thisrematch = $aaa[0]['rematch'];
                                                    $numreviews = $aaa[0]['numreviews'];
                                                    $numvotes = $aaa[0]['numvotes'];
                                                    $percentscore = $aaa[0]['percentscore'];
                                                    $thumbnail_filepath = $aaa[0]['thumbnail_filepath'];
                                                    $oldnumvotes = 'oldnumvotes' . $thisfight;
                                                    $oldpercentscore = 'oldpercentscore' . $thisfight;
                                                    $olduserrating = 'olduserrating' . $thisfight;
                                                ?>
                                                    <div style="padding:3px;">
                                                        <div style="background-color:#202020; border-radius:4px; text-align:left; height:300px;">
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <img src="<?php echo $thumbnail_filepath ?>" style="margin-bottom:0px;">
                                                            </a>
                                                            <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                <div class="sliderFightTitle" style="padding:0px; margin-bottom:15px; margin-top:11px;">
                                                                    <div class="underline-on-hover" style="padding-bottom:10px; font-weight:700;">
                                                                        <?php
                                                                        echo $aaa[0]['f1fn'] . " " . $aaa[0]['f1ln'] . " vs. " . $aaa[0]['f2fn'] . " " . $aaa[0]['f2ln'];
                                                                        if ($thisrematch > 1) {
                                                                            echo " $thisrematch";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <span class="sliderFightEvent"><?php echo $aaa[0]['promotion'] ?> <?php echo $aaa[0]['eventname'] ?></span>
                                                                    <br>
                                                                    <?php echo substr($aaa[0]['date'], 0, 4); ?> </span> -->
                                                                    <?php
                                                                    $date = date_create($aaa[0]['date']);
                                                                    ?>
                                                                    <span class="sliderFightEvent"><?php echo date_format($date, "F jS Y"); ?> </span>
                                                                </div>
                                                            </a>
                                                            <input type="hidden" id="<?php echo $oldnumvotes ?>" name="<?php echo $oldnumvotes ?>" value="<?php echo $numvotes ?>" />
                                                            <input type="hidden" id="<?php echo $oldpercentscore ?>" name="<?php echo $oldpercentscore ?>" value="<?php echo $percentscore ?>" />
                                                            <input type="hidden" id="<?php echo $olduserrating ?>" name="<?php echo $olduserrating ?>" value="<?php echo $ratingtoshow ?>" />
                                                            <div class="row" style="width:100%; margin-left:0px;">
                                                                <div class="col-xs-6" style="margin:0px; padding:0px;">
                                                                    <a href="https://fightingtomatoes.com/fight/<?php echo $aaa[0]['urlname'] ?>" style="text-decoration:none; outline: none;">
                                                                        <?php
                                                                        $thisyellowstarimg = "yellowstarimg-mobile" . $thislistid . "-" . $fightid;
                                                                        $thispercentscorespan = "percentscorespan-mobile" . $thislistid . "-" . $fightid;
                                                                        $thisratingspan = "ratingspan-mobile" . $thislistid . "-" . $fightid;
                                                                        if ($aaa[0]['percentscore'] >= 85) {
                                                                        ?>
                                                                            <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                                <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star-tomatoes.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                                <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                               
                                                                            </div>
                                                                        <?php
                                                                        } elseif ($aaa[0]['percentscore'] >= 70) {
                                                                        ?>
                                                                            <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                                <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/full-star.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                                <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                               
                                                                            </div>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <div title="Overall Score" class="yellowstarcontainer-thumbnailtype-mobile" style="min-width:100px;">
                                                                                <img id="<?php echo $thisyellowstarimg ?>" name="<?php echo $thisyellowstarimg ?>" src="https://fightingtomatoes.com/empty-star.png" class="yellowstar-thumbnailtype" style="padding-right:0px;" />
                                                                                <span class="thispercentscorespanclass-thumbnailtype" id="<?php echo $thispercentscorespan ?>" name="<?php echo $thispercentscorespan ?>"><?php echo $percentscore; ?></span>
                                                                                
                                                                            </div>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-6" style="margin:0px; padding:0px;">
                                                                    <?php
                                           
                                                                    $thisscorename = "score-mobile" . $thislistid . "-" . $fightid;
                                                                    $thisbluestarimg = "bluestarimg-mobile" . $thislistid . "-" . $fightid;
                                                                    $thisbluestarbutton = "bluestarbutton-mobile" . $thislistid . "-" . $fightid;
                                                                    if (($confirmedemail == '0') && ($signupmethod == 'email')) {
                                                                    ?>
                                                                        <div style="width:100%; margin:0px; padding:0px; ">
                                                                            <button data-toggle="modal" data-target="#myModalconfirmemail" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype-mobile">
                                                                                <img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                                <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"></span>
                                                                            </button>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        if ($hasengagedthisfight > 0) {       // if has voted yet.
                                                                        ?>
                                                                            <div style="width:100%; margin:0px; padding:0px; ">
                                                                                <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-thumbnailtype-mobile">
                                                                                    <img src="https://fightingtomatoes.com/blue-star-full.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                                    <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"><?php echo $ratingtoshow ?></span>
                                                                                </button>
                                                                            </div>
                                                                        <?php
                                                                        } else {      // has not voted on this fight yet.
                                                                        ?>
                                                                            <div style="width:100%; margin:0px; padding:0px; ">
                                                                                <button data-toggle="modal" data-target="#myModal" title="My Rating" id="<?php echo $thisbluestarbutton ?>" name="<?php echo $thisbluestarbutton ?>" onclick="storefightid('<?php echo $fightid ?>', '<?php echo $fighttitle ?>', 'thumbnail', 'mobile', '<?php echo $thislistid ?>');" class="ratebuttongrey-novote-thumbnailtype-mobile">
                                                                                    <img src="https://fightingtomatoes.com/blue-star.png" id="<?php echo $thisbluestarimg ?>" name="<?php echo $thisbluestarimg ?>" class="bluestar-novote-thumbnailtype-mobile" style="width:20px; height:20px; display:inline-block;" />
                                                                                    <span id="<?php echo $thisscorename ?>" name="<?php echo $thisscorename ?>" class="percentscore-thumbnail"></span>
                                                                                </button>
                                                                            </div>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                </div>
                            <?php
                            }
                            ?>
                            <br>
                        </div> <!--  end of col for slider -->
                    </div> <!-- end of row for sliders -->
                </div> <!-- end of mobile sliders -->
            </div>
        </div>
        <br>
        <br><br>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </div> <!-- /.container -->
    <footer class="footer">
        <div class="container" style="background-color:#222222; color:white; width:100%; padding-top:20px; padding-bottom:10px; text-align:center;">
            <p class="text-muted">
                DM: u/theconstantines on reddit
                <br class="mobile-break"><br class="mobile-break">
                <a href="PrivacyPolicy" style="color:inherit;" class="marginleftondesktop">Privacy Policy</a>
                <br class="mobile-break"><br class="mobile-break">
                <a href="termsofuse.php" style="color:inherit;" class="marginleftondesktop">Terms of Use</a>
                <br class="mobile-break"><br class="mobile-break">
                <a href="https://reddit.com/r/MMA/" target="_blank" style="color:inherit;" class="marginleftondesktop">Partners: r/MMA</a>
                <br class="mobile-break"><br class="mobile-break">
                <a href="https://fightingtomatoes.com/API" style="color:inherit;" class="marginleftondesktop">UFC Data API</a>
                <br class="mobile-break"><br class="mobile-break">
            </p>
        </div>
    </footer>
    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
</body>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick.min.js"></script>
<script>
    $(document).ready(function() {
        $('.carousel').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4,
            dots: true,
       
            prevArrow: "<button type='button' class='arrow-hover pull-left donotshow' style='margin-top:5px; height:300px; margin-left:-23px; border-radius: 3px; outline: none; border:none; '><img src='https://fightingtomatoes.com/arrow-left-666666.png' style='margin-left:-3px; height:55px' /></button>",
            nextArrow: "<button type='button' class='arrow-hover pull-right' style='margin-top:-300px; height:300px; margin-right:-73px; border-radius:3px; border:none;'><img src='https://fightingtomatoes.com/arrow-right-666666.png' style='margin-left:-3px; height:55px' /></button>",
            centerMode: false,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false,
                        arrows: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        dots: false,
                        arrows: false
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        dots: false,
                        arrows: false
                    }
                }
       
            ]
        });
    });
    $(document).ready(function() {
        $('.carouselCustom').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4,
            dots: true,
      
            prevArrow: "<button type='button' class='arrow-hover pull-left donotshow' style='margin-top:5px; height:300px; margin-left:-23px; border-radius: 3px; outline: none; border:none; '><img src='https://fightingtomatoes.com/arrow-left-666666.png' style='margin-left:-3px; height:55px' /></button>",
            nextArrow: "<button type='button' class='arrow-hover pull-right' style='margin-top:-235px; height:200px; margin-right:-73px; border-radius:3px; border:none;'><img src='https://fightingtomatoes.com/arrow-right-666666.png' style='margin-left:-3px; height:55px' /></button>",
            centerMode: false,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false,
                        arrows: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        dots: false,
                        arrows: false
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        dots: false,
                        arrows: false
                    }
                }
   
            ]
        });
    });
    var storedrating; // declares as global variablec
    var storedfightid; // declares as global variable
    var css_type_thumbnail_or_standard; // declares as global variable
    var desktop_or_mobile; // declares as global variable
    var listid; // declares as global variable
    function wantsemail(wants) {
        document.getElementById("wantsemail").style.display = "none";
        if (wants == 'Yes, please.') {
          
            wantint = 1;
        } else {
          
            wantint = 0;
        }
        clickinguser = '<?php echo $loggeduser ?>';
        xmlhttp2 = new XMLHttpRequest(); 
  
        xmlhttp2.onreadystatechange = function() {
            if (xmlhttp2.readyState == 4) {
           
            }
        }
        xmlhttp2.open("GET", "wantsemail.php?clickinguser=" + clickinguser + "&wantsemail=" + wantint, true); // opens and sends variable.
        xmlhttp2.send();
    }
    function goToNewPage() {
        var url = document.getElementById('list').value;
        if (url != 'none') {
            window.location = url;
        }
    }
    function storefightid(fightidtostore, fighttitle, css_type_thumbnail_or_standard, desktop_or_mobile, listid) {
       
        window.storedfightid = fightidtostore;
        window.css_type_thumbnail_or_standard = css_type_thumbnail_or_standard;
        window.desktop_or_mobile = desktop_or_mobile;
        window.listid = listid;
        document.getElementById('myModalLabel').textContent = fighttitle;
  
    }
    function storerating(ratingtostore) {
        window.storedrating = ratingtostore.value;
    }
    function insertcorrectfighttitle(fighttitle) {
  
        document.getElementById('myModalLabel').textContent = fighttitle;
  
    }
    function enterrating() {
        if (storedrating === undefined) {
        
            return;
        }
        xmlhttp2 = new XMLHttpRequest(); 
        xmlhttp2.onreadystatechange = function() {
            if (xmlhttp2.readyState == 4) {
         
            }
        }
        xmlhttp2.open("GET", "rate.php?r=" + storedrating + "&nfi=" + storedfightid, true); // opens and sends variable.
        xmlhttp2.send();
        if (css_type_thumbnail_or_standard == 'standard') {
            bluestarbuttonelementid = 'bluestarbutton' + storedfightid;
        } else {
            if (desktop_or_mobile == 'mobile') {
      
                bluestarbuttonelementid = 'bluestarbutton-mobile' + listid + '-' + storedfightid;
            } else {
                bluestarbuttonelementid = 'bluestarbutton' + listid + '-' + storedfightid;
            }
        }
        if (css_type_thumbnail_or_standard == 'standard') {
            bluestarimgelementid = 'bluestarimg' + storedfightid;
        } else {
            if (desktop_or_mobile == 'mobile') {
          
                bluestarimgelementid = 'bluestarimg-mobile' + listid + '-' + storedfightid;
            } else {
                bluestarimgelementid = 'bluestarimg' + listid + '-' + storedfightid;
            }
        }
        if (css_type_thumbnail_or_standard == 'standard') {
            yellowstarimgelementid = 'yellowstarimg' + storedfightid;
        } else {
            if (desktop_or_mobile == 'mobile') {
          
                yellowstarimgelementid = 'yellowstarimg-mobile' + listid + '-' + storedfightid;
            } else {
                yellowstarimgelementid = 'yellowstarimg' + listid + '-' + storedfightid;
            }
        }
        if (css_type_thumbnail_or_standard == 'standard') {
            userratingelementid = 'score' + storedfightid;
        } else {
            if (desktop_or_mobile == 'mobile') {
                userratingelementid = 'score-mobile' + listid + '-' + storedfightid;
            } else {
                userratingelementid = 'score' + listid + '-' + storedfightid;
            }
        }
        // HERE DIFFERENTIATE BETWEEN IF IT'S A THUMBNAIL DESIGN OR STANDARD DESIGN. IF THUMBNAIL, BLUESTAR CLASS SHOULD BE BLUESTAR-THUMBNAIL
        if (css_type_thumbnail_or_standard == 'thumbnail') {
          
            document.getElementById(userratingelementid).textContent = storedrating;
            document.getElementById(bluestarimgelementid).src = 'https://fightingtomatoes.com/blue-star-full.png';
      
            document.getElementById(bluestarimgelementid).className = 'bluestar-thumbnailtype';
            document.getElementById(bluestarbuttonelementid).className = 'ratebuttongrey-thumbnailtype';
            var f = document.getElementById(bluestarbuttonelementid);
            setTimeout(function() {
                f.className = (f.className == 'buttongreyed-thumbnailtype' ? '' : 'buttongreyed-thumbnailtype');
            }, 250);
            setTimeout(function() {
                f.className = (f.className == 'buttongreyedintermediate-thumbnailtype' ? '' : 'buttongreyedintermediate-thumbnailtype');
            }, 1000);
            setTimeout(function() {
                f.className = (f.className == 'ratebuttongrey-thumbnailtype' ? '' : 'ratebuttongrey-thumbnailtype');
            }, 1200);
        } else {
          
            document.getElementById(userratingelementid).textContent = storedrating;
            document.getElementById(bluestarimgelementid).src = 'https://fightingtomatoes.com/blue-star-full.png';
            document.getElementById(bluestarimgelementid).className = 'bluestar';
            document.getElementById(bluestarbuttonelementid).className = 'ratebuttongrey';
            var f = document.getElementById(bluestarbuttonelementid);
            setTimeout(function() {
                f.className = (f.className == 'buttongreyed' ? '' : 'buttongreyed');
            }, 250);
            setTimeout(function() {
                f.className = (f.className == 'buttongreyedintermediate' ? '' : 'buttongreyedintermediate');
            }, 1000);
            setTimeout(function() {
                f.className = (f.className == 'ratebuttongrey' ? '' : 'ratebuttongrey');
            }, 1200);
        }
        userratingimgelementid = 'img' + storedfightid;
        // IF IS FIRST VOTE EVER
        if (this.fightidarrayjavascript[storedfightid] == 0) {
   
            numvoteselementid = 'numvotes' + storedfightid;
            oldnumvoteselementid = 'oldnumvotes' + storedfightid;
            ratingspanelementid = 'ratingspan' + storedfightid;
            oldpercentscoreelementid = 'oldpercentscore' + storedfightid;
            olduserratingelementid = 'olduserrating' + storedfightid;
            if (css_type_thumbnail_or_standard == 'standard') {
                percentscorespanelementit = 'percentscorespan' + storedfightid;
                ratingspanelementid = 'ratingspan' + storedfightid;
            } else {
                if (desktop_or_mobile == 'mobile') {
                 
                    percentscorespanelementit = 'percentscorespan-mobile' + listid + '-' + storedfightid;
                    ratingspanelementid = 'ratingspan-mobile' + listid + '-' + storedfightid;
                } else {
                    percentscorespanelementit = 'percentscorespan' + listid + '-' + storedfightid;
                    ratingspanelementid = 'ratingspan' + listid + '-' + storedfightid;
                }
            }
         
            var oldnumvotes = +document.getElementById(oldnumvoteselementid).value;
           
            // set new number of votes
            newnumvotes = oldnumvotes + 1;
 
            var oldpercentscore = +document.getElementById(oldpercentscoreelementid).value;
            var incomingpoints = storedrating * 10;
            var oldtotalpoints = oldpercentscore * oldnumvotes;
            var newtotalpoints = oldtotalpoints + incomingpoints;
            var newpercentscore = newtotalpoints / newnumvotes;
       
            document.getElementById(percentscorespanelementit).textContent = Math.round(newpercentscore);
            if (newpercentscore >= 85) {
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/full-star-tomatoes.png";
            } else if (newpercentscore >= 70) {
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/full-star.png";
            } else {
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/empty-star.png";
            }
            fightratingimgelementid = 'fightratingimg' + storedfightid;
.
            window.firstvotehappenedarrayjavascript[storedfightid] = 1;
        } else { 
            numvoteselementid = 'numvotes' + storedfightid;
            // NOW CHANGE VISIBLE SCORE
            if (firstvotehappenedarrayjavascript[storedfightid] === 1) {
              
                oldnumvoteselementid = 'oldnumvotes' + storedfightid;
                var oldnumvotes = +document.getElementById(oldnumvoteselementid).value;
                oldnumvotes = oldnumvotes + 1;
 
                var olduserrating = existingvotejavascript[storedfightid];
                var oldpercentscore = currentpercentscore[storedfightid];
            } else {
          
                oldnumvoteselementid = 'oldnumvotes' + storedfightid;
                var oldnumvotes = +document.getElementById(oldnumvoteselementid).value;
              
                olduserratingelementid = 'olduserrating' + storedfightid;
           
                var olduserrating = +document.getElementById(olduserratingelementid).value;
          
                oldpercentscoreelementid = 'oldpercentscore' + storedfightid;
          
                var oldpercentscore = +document.getElementById(oldpercentscoreelementid).value;
      
                if (css_type_thumbnail_or_standard == 'standard') {
                    percentscorespanelementit = 'percentscorespan' + storedfightid;
                } else {
                    if (desktop_or_mobile == 'mobile') {
                    
                        percentscorespanelementit = 'percentscorespan-mobile' + listid + '-' + storedfightid;
                    } else {
                        percentscorespanelementit = 'percentscorespan' + listid + '-' + storedfightid;
                    }
                }
            }
            var incomingpoints = storedrating * 10;
            var outgoingpoints = olduserrating * 10;
            var oldtotalpoints = oldpercentscore * oldnumvotes;
            var newtotalpoints = oldtotalpoints + incomingpoints - outgoingpoints;
            var newpercentscore = newtotalpoints / oldnumvotes;
  
            document.getElementById(percentscorespanelementit).textContent = Math.round(newpercentscore);
          
            if (newpercentscore >= 85) {
               
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/full-star-tomatoes.png";
           
            } else if (newpercentscore >= 70) {
              
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/full-star.png";
                
            } else {
              
                document.getElementById(yellowstarimgelementid).src = "https://fightingtomatoes.com/empty-star.png";
           
            }
            fightratingimgelementid = 'fightratingimg' + storedfightid;
    
        }
    }
  
    function gotologin() {
        location.href = 'https://fightingtomatoes.com/login.php';
    }
    function resendconfirmationemail() {
        location.href = 'https://fightingtomatoes.com/resendconfirmationemail.php';
    }
</script>
</html>

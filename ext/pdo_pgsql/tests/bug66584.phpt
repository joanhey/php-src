--TEST--
PDO PgSQL Bug #66584 (Segmentation fault on statement deallocation)
--EXTENSIONS--
pdo_pgsql
--SKIPIF--
<?php
require __DIR__ . '/config.inc';
require __DIR__ . '/../../../ext/pdo/tests/pdo_test.inc';
PDOTest::skip();
?>
--FILE--
<?php
require __DIR__ . '/../../../ext/pdo/tests/pdo_test.inc';
$pdo = PDOTest::test_factory(__DIR__ . '/common.phpt');

$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$pdo->beginTransaction();

$pdo->query("CREATE TABLE test66584 (a int)");
$pdo->query("INSERT INTO test66584 VALUES (165)");

for ($i = 1; $i >= 0; $i--) {
    $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, (bool)$i);

    try {
        run($pdo, [0 => 1, 2 => 165, 5 => 3]);
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }

    try {
        run($pdo, json_decode('{"0":234,"1":165,"2":221,"3":207,"4":188,"5":216,"6":1150,"7":916,"8":967,"9":987,"10":951,"11":990,"12":959,"13":896,"14":947,"15":877,"16":1000,"17":1023,"18":904,"19":856,"20":860,"21":866,"22":930,"23":974,"24":1032,"25":1016,"26":1050,"27":1059,"28":1040,"29":1064,"30":1004,"31":214,"32":189,"33":166,"34":1002,"35":167,"36":191,"37":859,"38":204,"39":181,"40":1001,"42":208,"43":198,"44":177,"45":1003,"46":858,"47":190,"48":162,"49":210,"50":171,"51":197,"52":168,"53":194,"54":209,"55":200,"56":192,"57":180,"58":232,"59":222,"60":163,"61":196,"62":217,"64":176,"65":193,"66":172,"67":195,"68":170,"69":173,"70":233,"71":223,"72":218,"73":186,"74":175,"75":224,"76":205,"77":211,"78":235,"79":1101,"80":225,"81":236,"82":1102,"83":1164,"84":1083,"85":1005,"86":861,"87":1179,"88":960,"89":991,"90":1187,"91":880,"92":1149,"93":1033,"94":931,"95":1006,"96":862,"97":1151,"98":917,"99":881,"100":1148,"101":1065,"102":867,"103":952,"104":1152,"105":918,"106":961,"107":1180,"108":992,"109":1188,"110":932,"111":933,"112":968,"113":868,"114":882,"115":1147,"116":1017,"117":1131,"118":1174,"119":1178,"120":1186,"121":869,"122":1051,"123":934,"124":969,"125":975,"126":1066,"127":237,"128":953,"129":1024,"130":1146,"131":883,"132":1145,"133":884,"134":885,"135":1144,"136":886,"137":1143,"138":1025,"139":897,"140":898,"141":899,"142":1026,"143":1142,"144":887,"145":1141,"146":888,"147":889,"148":1140,"149":1189,"150":993,"151":1139,"152":890,"153":1138,"154":891,"155":900,"156":892,"157":1137,"158":1027,"159":901,"160":1136,"161":893,"162":870,"163":1052,"164":954,"165":1041,"166":1018,"167":1165,"168":1084,"169":962,"170":1181,"171":994,"172":1190,"173":1042,"174":935,"175":226,"176":871,"177":1191,"178":995,"179":977,"180":948,"181":1175,"182":1053,"183":955,"184":1182,"185":963,"186":1067,"187":919,"188":1153,"189":920,"190":1154,"191":1055,"192":1054,"193":1056,"194":863,"195":872,"196":1028,"197":921,"198":1155,"199":936,"200":970,"201":1019,"202":1166,"203":1085,"204":1135,"205":894,"206":1034,"207":905,"208":873,"209":937,"210":902,"211":1029,"212":1007,"213":864,"214":1043,"215":1057,"216":956,"217":957,"218":939,"219":1086,"220":1167,"221":1087,"222":1168,"223":1173,"224":1108,"225":978,"226":1044,"227":1183,"228":964,"229":965,"230":1184,"231":1045,"232":874,"233":940,"234":1046,"235":979,"236":903,"237":980,"238":1156,"239":922,"240":1035,"241":906,"242":971,"243":972,"244":878,"245":1134,"246":879,"247":1133,"248":907,"249":1036,"250":908,"251":1132,"252":895,"253":909,"254":1060,"255":981,"256":1068,"257":996,"258":1192,"259":941,"260":865,"261":1008,"262":910,"263":997,"264":1193,"265":982,"266":942,"267":1020,"268":983,"269":1061,"270":949,"271":1176,"272":875,"273":911,"274":1069,"275":1157,"276":923,"277":1158,"278":924,"279":988,"280":984,"281":925,"282":1159,"283":1062,"284":1047,"285":1194,"286":998,"287":1021,"288":1030,"289":1031,"290":1070,"291":1088,"292":1169,"293":958,"294":1195,"295":999,"296":966,"297":1185,"298":944,"299":945,"300":1022,"301":1103,"302":220,"303":1099,"304":1048,"305":927,"306":1161,"307":989,"308":973,"309":1071,"310":1074,"311":1072,"312":1073,"313":912,"314":1037,"315":913,"316":914,"317":1177,"318":950,"319":1049,"320":876,"321":985,"322":915,"323":1038,"324":946,"325":1089,"326":1170,"327":1090,"328":1171,"329":1091,"330":1172,"331":1063,"332":986,"333":928,"334":1162,"335":929,"336":1163,"337":976,"338":231,"339":201,"340":1098,"341":215}', true));
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
}

try {
    $pdo->query("DROP TABLE test66584");
    $pdo->rollback();
} catch (\Exception $e) {
}

function run($pdo, $data)
{
    $bind = join(', ', array_fill(0, count($data), '?'));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM test66584 WHERE a IN ({$bind})");

    var_dump(count($data));

    $stmt->execute($data);

    var_dump($stmt->fetchColumn());
}

?>
--CLEAN--
<?php
require __DIR__ . '/../../../ext/pdo/tests/pdo_test.inc';
$pdo = PDOTest::test_factory(__DIR__ . '/common.phpt');
$pdo->query("DROP TABLE IF EXISTS test66584");
?>
--EXPECTF--
int(3)
string(%d) "SQLSTATE%s"
int(340)
string(%d) "SQLSTATE%s"
int(3)
string(%d) "SQLSTATE%s"
int(340)
string(%d) "SQLSTATE%s"

--TEST--
mysqli_stmt_send_long_data() - exceed packet size, mysqlnd
--EXTENSIONS--
mysqli
--SKIPIF--
<?php
require_once 'skipifconnectfailure.inc';
?>
--FILE--
<?php
    require 'table.inc';

    if (!$stmt = mysqli_stmt_init($link))
        printf("[001] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

    if (!mysqli_query($link, "DROP TABLE IF EXISTS test"))
        printf("[002] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

    if (!mysqli_query($link, sprintf("CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT, label LONGBLOB, PRIMARY KEY(id)) ENGINE = %s", $engine)))
        printf("[003] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

    if (!mysqli_stmt_prepare($stmt, "INSERT INTO test(id, label) VALUES (?, ?)"))
        printf("[004] [%d] %s\n", mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));

    $id = null;
    $label = null;
    if (!mysqli_stmt_bind_param($stmt, "ib", $id, $label))
        printf("[005] [%d] %s\n", mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));

    if (!$res = mysqli_query($link, "SHOW VARIABLES LIKE 'max_allowed_packet'"))
        printf("[006] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

    if (!$row = mysqli_fetch_assoc($res))
        printf("[007] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

    mysqli_free_result($res);

    if (0 === ($max_allowed_packet = (int)$row['Value']))
        printf("[008] Cannot determine max_allowed_packet size and/or bogus max_allowed_packet setting used.\n");

    // let's ignore upper limits for LONGBLOB (2^32) ...
    // maximum packet size up to which we test is 10M
    $tmp = str_repeat('a', 1024);

    $limit = min(floor($max_allowed_packet / 1024 / 2), 10240);
    $blob = str_repeat($tmp, $limit);

    assert(strlen($blob) <= $max_allowed_packet);

    if (true !== ($tmp = mysqli_stmt_send_long_data($stmt, 1, $blob)))
        printf("[009] Expecting boolean/true, got %s/%s. [%d] %s\n",
            gettype($tmp), $tmp, mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));

    $id = 1;
    if (true !== mysqli_stmt_execute($stmt))
        printf("[010] [%d] %s\n", mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));

        /*
        TODO - we skip this because of the open bug http://bugs.mysql.com/bug.php?id=26824
        It would always fail.

        This should be added to the EXPECTF, if you reactivate the test
Warning: mysqli_stmt_send_long_data(): Skipped %d bytes. Last command STMT_SEND_LONG_DATA hasn't consumed all the output from the server in %s on line %d

Warning: mysqli_stmt_send_long_data(): There was an error while sending long data. Probably max_allowed_packet_size is smaller than the data. You have to increase it or send smaller chunks of data. Answer was %d bytes long. in %s on line %d



        if (floor($max_allowed_packet / 1024 / 2) <= 10240) {
                // test with a blob smaller than 10M allows us to test
                // for too long packages without wasting too much memory
                $limit = $max_allowed_packet - strlen($blob) + 1;
                $blob2 = $blob;
        $blob2 .= str_repeat('b', $limit);

                assert(strlen($blob2) > $max_allowed_packet);

                if (false !== ($tmp = mysqli_stmt_send_long_data($stmt, 1, $blob2)))
                        printf("[011] Expecting boolean/false, got %s/%s. [%d] %s\n",
                                gettype($tmp), $tmp, mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));

                $id = 2;
                if (false !== ($tmp = mysqli_stmt_execute($stmt)))
                        printf("[012] Expecting boolean/false, got %s/%s, [%d] %s\n",
                                gettype($tmp), $tmp, mysqli_stmt_errno($stmt), mysqli_stmt_error($stmt));
        }
        */
    mysqli_stmt_close($stmt);
    mysqli_close($link);

    print "done!";
?>
--CLEAN--
<?php
    require_once 'clean_table.inc';
?>
--EXPECT--
done!

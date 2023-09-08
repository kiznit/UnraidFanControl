<?php
/*
    Copyright (c) 2023, Thierry Tremblay
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this
    list of conditions and the following disclaimer.

    * Redistributions in binary form must reproduce the above copyright notice,
    this list of conditions and the following disclaimer in the documentation
    and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
    IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
    FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
    DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
    SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
    CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
    OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

$dev = $_SERVER['DOCUMENT_ROOT'] ? false : true;

function read_sensors() {
    $sensors = json_decode(file_get_contents("sensors.json"), true);
    return $sensors;
}

function detect_sensors() {
    global $dev;
    $bus = NULL;
    $adapter = NULL;

    $raw = $dev
        ? file_get_contents("sensors.txt")
        : shell_exec("sensors");
    
    foreach(explode("\n", $raw) as $line) {
        $pos = strpos($line, "(");
        if ($pos) $line = substr($line, 0, $pos);
        $line = trim(preg_replace("/[\s:+]+/", " ", $line));

        if (!$line) {
            if ($bus)
                echo "\n";
            $bus = NULL;
            $adapter = NULL;
            continue;
        }

        if (!$bus) {
            $bus = $line;
            echo "BUS: ", $line, "\n";
        }
        else if (!$adapter) {
            $adapter = $line;
            echo "ADAPTER: ", $line, "\n";
        }
        else
        {
            echo "SENSOR: ", $line, "\n";
        }
    }
}

if ($dev)
    detect_sensors();
?>
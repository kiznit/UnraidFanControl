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

function endswith($haystack, $needle) {
    $length = strlen($needle);
    if( !$length ) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

function detect_sensors() {
    global $dev;
    $bus = NULL;
    $adapter = NULL;
    $sensors = array(
        "fans" => array(),
        "temps" => array(),
    );

    $raw = $dev
        ? file_get_contents("sensors.txt")
        : shell_exec("sensors");
    
    foreach(explode("\n", $raw) as $line) {
        // Detect end of section
        if (!$line) {
            $bus = NULL;
            $adapter = NULL;
            continue;
        }

        // Strip things
        $pos = strpos($line, "(");
        if ($pos) $line = substr($line, 0, $pos);
        $line = trim(preg_replace("/[\s+]+/", " ", $line));

        if (!$line) {
            continue;
        }

        // New bus?
        if (!$bus) {
            $bus = $line;
            continue;
        }

        // New adapter?
        if (!$adapter) {
            $adapter = trim(explode(':', $line)[1]);
            continue;
        }

        $parts = explode(":", $line);
        if (count($parts) == 2) {
            $name = $parts[0];
            $value = $parts[1];

            if (endswith($value, "C")) {
                $value = floatval($value);
                array_push($sensors["temps"], array(
                    "bus" => $bus,
                    "adapter" => $adapter,
                    "name" => $name,
                    "value" => $value,
                ));
            }
            else if (endswith($line, " RPM")) {
                $value = floatval($value);
                array_push($sensors["fans"], array(
                    "bus" => $bus,
                    "adapter" => $adapter,
                    "name" => $name,
                    "value" => $value,
                ));
            }
        }
    }

    sort($sensors['fans']);
    sort($sensors['temps']);

    return $sensors;
}


if ($dev) {
    $sensors = detect_sensors();
    echo print_r($sensors);
}
?>

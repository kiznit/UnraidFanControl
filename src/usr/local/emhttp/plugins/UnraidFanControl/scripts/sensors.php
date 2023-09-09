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

function read_sensors() {
    global $dev;
    $raw = $dev
        ? file_get_contents("sensors.json")
        : shell_exec("sensors -j");
    
    $source = json_decode($raw, true);
    $sensors = Array();
    foreach($source as $bus => $data) {
        $sensors[$bus] = Array();
        foreach($data as $name => $properties) {
            if ($name == "Adapter") {
                continue;
            }
            foreach ($properties as $property => $value) {
                if (endswith($property, "_input")) {
                    $sensors[$bus][$name] = $value;
                }
            }
        }
    }

    return $sensors;
}

echo print_r(json_encode(read_sensors()), true);
?>

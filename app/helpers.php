<?php
function translateToEnglish($validation) {
    $condition = $validation->image_type;
    $restriction_params = $validation->restriction_params;
    $restriction_values = $validation->restriction_values;
    $notes = $validation->notes;

    $return = [];

    switch($condition) {
        case '.jpg':
        case '.mp4':
        case '.mp3':
        case '.gif':
        case '.mov':
            $return = [
                'image_type' => $condition,
                'restrictions' => checkConditions($restriction_params, $restriction_values),
                'notes' => $notes
            ];
            break;
        default:
            break;
    }

    return $return;
}

function checkConditions($restriction_params, $restriction_values) {
    $result = '';

    switch($restriction_params) {
        case 'aspect_ratio':
            $result = config('global.translators')[$restriction_params] . $restriction_values;
            break;
        case 'size':
            $restriction_values = explode(' ', $restriction_values);
            $check = config('global.translators')[array_shift($restriction_values)];
            $restriction_values = implode(' ', $restriction_values);

            $result = config('global.translators')[$restriction_params] . $check . $restriction_values;
            break;
        case 'length':
            $restriction_values = explode(' ', $restriction_values);
            $check = config('global.translators')[array_shift($restriction_values)];
            $restriction_values = implode(' ', $restriction_values);

            $result = config('global.translators')[$restriction_params] . $check . $restriction_values;
            break;
        default:
            break;
    }

    return $result;
}

function getSizeInBytes(string $from): ?int {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $number = substr($from, 0, -2);
    $suffix = strtoupper(substr($from,-2));

    //B or no suffix
    if(is_numeric(substr($suffix, 0, 1))) {
        return preg_replace('/[^\d]/', '', $from);
    }

    $exponent = array_flip($units)[$suffix] ?? null;
    if($exponent === null) {
        return null;
    }

    return $number * (1024 ** $exponent);
}

function getTimeInSeconds(string $from): ?int {
    $units = ['SECONDS', 'MINUTES', 'HOURS'];

    $from = preg_replace('/\s+/ismx', ' ', trim($from));
    $from = explode(' ', $from);
    $number = $from[0];
    $suffix = strtoupper($from[1]) ?? '';

    if(empty($suffix)) {
        return $number;
    }

    $exponent = array_flip($units)[$suffix] ?? null;
    if($exponent === null) {
        return null;
    }

    return $number * (60 ** $exponent);
}

function checkAspectRatio($values, $image) {
    $values = explode(':', $values);

    list($width, $height) = getimagesize($image);

    $aspectCheck = ($width/$height) * ($values[1] / $values[0]);

    return $aspectCheck == 1;
}

function checkFileSize($values, $uploadedFileSize) {
    $values = explode(' ', $values);
    $identifier = array_shift($values); // get the identifier
    $values = implode(' ', $values);

    $expectedFileSize = getSizeInBytes($values);
    //echo $uploadedFileSize . '---' . $expectedFileSize;
    $result = false;
    switch($identifier) {
        case '<':
            $result = ( $uploadedFileSize < $expectedFileSize );
            break;
        case '>':
            $result = ( $uploadedFileSize > $expectedFileSize );
            break;
        case '<=':
            $result = ( $uploadedFileSize <= $expectedFileSize );
            break;
        case '>=':
            $result = ( $uploadedFileSize >= $expectedFileSize );
            break;
        default:
        $result = ( $uploadedFileSize < $expectedFileSize );
            break;
    }

    return $result;
}

function checkContentLength($values, $media) {
    $values = explode(' ', $values);
    $identifier = array_shift($values); // get the identifier
    $values = implode(' ', $values);

    $result = false;

    $getID3 = new \getID3;
    $info = $getID3->analyze($media);

    $lengthInSeconds = $info['playtime_seconds'];
    $expectedLength = getTimeInSeconds($values);
    //echo $lengthInSeconds . '----' . $expectedLength;
    switch($identifier) {
        case '<':
            $result = ( $lengthInSeconds < $expectedLength );
            break;
        case '>':
            $result = ( $lengthInSeconds > $expectedLength );
            break;
        case '<=':
            $result = ( $lengthInSeconds <= $expectedLength );
            break;
        case '>=':
            $result = ( $lengthInSeconds >= $expectedLength );
            break;
        default:
        $result = ( $uploadedFileSize < $expectedFileSize );
            break;
    }

    return $result;
}

function getScreenshot($file_path, $savePath) {
    $ffmpeg = \FFMpeg\FFMpeg::create([
        'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
        'ffprobe.binaries' => '/usr/bin/ffprobe'
    ]);
    $video = $ffmpeg->open($file_path);

    $getID3 = new \getID3;
    $info = $getID3->analyze($file_path);

    $middleOfVideo = ceil( $info['playtime_seconds'] / 2 );

    $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($middleOfVideo));
    $frame->save($savePath);
}
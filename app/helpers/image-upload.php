<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


if (!function_exists('upload_image')) {

    function upload_image(string $dir, string $format, $image = null)
    {
        if ($image) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }
}

if (!function_exists('update_image')) {

    function update_image(string $dir, $old_image, string $format, $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        return upload_image($dir, $format, $image);
    }

}
if (!function_exists('move_image')) {

    function move_image($from_full_path, $to_path, $file)
    {
        delete_old_temp_image();
        $file = collect(explode('/', $file))->last();
        if (!Storage::disk('public')->exists($to_path)) {
            Storage::disk('public')->makeDirectory($to_path);
        }
        if (Storage::disk('public')->exists($from_full_path)) {
            try {
                Storage::disk('public')->copy($from_full_path, $to_path . $file);
                return $file;
            } catch (\Throwable $th) {
                return $file;
            }
        }
        return 'def.png';
    }
}

if (!function_exists('upload_temp')) {
    function upload_temp($image)
    {
        if ($image) {
            try {
                $file = upload_image('temp/', $image->extension(), $image);
                return [
                    'status' => true,
                    'file' => $file,
                    'url' => asset('storage/app/public/temp/' . $file),
                ];
            } catch (\Throwable $th) {
                return [
                    'status' => false,
                    'message' => $th->getMessage(),
                    'url' => asset('assets/back-end/img/400x400/img2.jpg'),
                ];
            }
        }
        return [
            'status' => false,
            'url' => asset('assets/back-end/img/400x400/img2.jpg'),
        ];
    }
}

if (!function_exists('delete_old_temp_image')) {

    function delete_old_temp_image()
    {
        $files = collect();
        foreach (Storage::disk('public')->allFiles('temp') as $file) {
            $filename = public_path('storage/' . $file);

            if (file_exists($filename)) {
                $data = File::lastModified($filename);

                if (now() > Carbon::parse($data)->addDays(2)) {
                    unlink($filename);
                } else {
                    $files->add([
                        'date' => Carbon::parse($data)->format('Y-m-d'),
                        'file' => $filename,
                        'can_delete' => now() > Carbon::parse($data)->addDays(2)
                    ]);
                }
            }
        }
        return $files;
    }
}
if (!function_exists('delete_image')) {
    function delete_image($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }

        return [
            'success' => 1,
            'message' => ('File Removed successfully !')
        ];

    }
}

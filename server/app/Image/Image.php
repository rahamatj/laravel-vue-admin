<?php

namespace App\Image;

use Illuminate\Support\Str;
use Image as Intervention;

class Image
{
    protected $image;

    protected $directory = 'images';
    protected $filename = 'image';
    protected $resolution = [];

    public function __construct($image) {
        $this->image = $image;
    }

    public function setDirectory($directory) {
        $this->directory = $directory;
    }

    public function setResolution(array $options = []) {
        $this->resolution['width'] = $options['width'];
        $this->resolution['height'] = $options['height'];
    }

    public function setFileName($filename) {
        $this->filename = Str::slug($filename) . '-' . date('Y-m-d-H-i-s');
    }

    public function getBaseName() {
        return $this->filename . '.' . $this->image->getClientOriginalExtension();
    }

    public function getFullName() {
        return $this->directory . '/' . $this->getBaseName();
    }

    public function save(array $options = []) {

        if (isset($options['directory'])) {
            $this->setDirectory($options['directory']);
        }

        if (isset($options['filename'])) {
            $this->setFileName($options['filename']);
        }

        if (isset($options['resolution'])) {
            $this->setResolution($options['resolution']);
        }

        $intervention = Intervention::make($this->image->getRealPath());

        if (isset($this->resolution['width']) && isset($this->resolution['height']))
        {
            $intervention->resize($this->resolution['width'], $this->resolution['height'],
                    function($constraint) {
                        $constraint->aspectRatio();
                    });
        }

        $intervention->save($this->getFullName());
    }
}

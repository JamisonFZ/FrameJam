<?php

namespace FrameJam\Core\Upload;

class FileUploader
{
    private array $allowedTypes = [];
    private int $maxSize = 5242880; // 5MB
    private string $uploadPath;

    public function __construct(string $uploadPath)
    {
        $this->uploadPath = $uploadPath ?? __DIR__ . '/../../storage/uploads';
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    public function setAllowedTypes(array $types): self
    {
        $this->allowedTypes = $types;
        return $this;
    }

    public function setMaxSize(int $size): self
    {
        $this->maxSize = $size;
        return $this;
    }

    public function upload(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('Arquivo inválido');
        }

        if ($file['size'] > $this->maxSize) {
            throw new \Exception('Arquivo muito grande');
        }

        if (!empty($this->allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $this->allowedTypes)) {
                throw new \Exception('Tipo de arquivo não permitido');
            }
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $this->uploadPath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Erro ao mover o arquivo');
        }

        return [
            'filename' => $filename,
            'path' => $destination,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }

    public function delete(string $filename): bool
    {
        $file = $this->uploadPath . '/' . $filename;
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }
} 
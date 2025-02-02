<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

class MigrationsLocation
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param string $directory
     * @param string $namespace
     */
    public function __construct($directory, $namespace)
    {
        $this->directory = $directory;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}

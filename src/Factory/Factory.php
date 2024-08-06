<?php

namespace Karlhsu\Yidun\Factory;

use Karlhsu\Yidun\Service\AudioService;
use Karlhsu\Yidun\Service\ImageService;
use Karlhsu\Yidun\Service\TextService;
use Karlhsu\Yidun\Service\VideoService;
use Throwable;

/**
 * Class Factory.
 *
 * @method static TextService   text($config)
 * @method static ImageService  image($config)
 * @method static AudioService  audio($config)
 * @method static VideoService  video($config)
 */
class Factory
{
    const ServiceMap = [
        'text'  => TextService::class,
        'image' => ImageService::class,
        'audio' => AudioService::class,
        'video' => VideoService::class,
    ];

    /**
     * create service
     *
     * @param string $service
     * @param array $arguments
     * @return TextService|ImageService|AudioService|VideoService|void
     * @author KarlHsu
     */
    public static function makeService(string $service, array $arguments)
    {
        try {
            $path = self::ServiceMap[$service];
            return new $path($arguments);
        } catch (Throwable $exception) {
            echo 'yidun.service.exception'.$exception->getMessage();
        }
    }

    /**
     * Dynamically pass methods to the application.
     * @throws Throwable
     * @author KarlHsu
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return self::makeService($name, $arguments[0] ?? []);
    }
}

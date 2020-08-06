<?php

namespace JsonParser;

use JsonStreamingParser\Listener\ListenerInterface;
use phpDocumentor\Reflection\Types\Callable_;

class StreamingListener implements ListenerInterface
{
    /** @var resource */
    private $stream;

    private bool $keyHasBeenWritten = false;
    private string $lastKey = '';

    /** @var callable */
    private $callback;


    /**
     * @param resource $stream
     * @param callable $callback
     * @throws \Throwable
     */
    public function __construct($stream, callable $callback)
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException(sprintf(
                'You must provide a stream as an argument, [%s] given.',
                gettype($stream)
            ));
        }

        $meta = stream_get_meta_data($stream);

        if ($meta['mode'] !== 'w+') {
            throw new \Exception('You must specify the stream mode as w+');
        }

        $this->callback = $callback;
        $this->stream = $stream;
    }

    public function endDocument(): void
    {
        fseek($this->stream, -1, SEEK_END);

        if (fread($this->stream,1) === ',') {
            ftruncate($this->stream, fstat($this->stream)['size']);
        }
    }

    public function startObject(): void
    {
        fwrite($this->stream, '{');
    }

    public function endObject(): void
    {
        $this->clearLastCommaOccurrence();

        fwrite($this->stream, '},');
    }

    public function startArray(): void
    {
        fwrite($this->stream, '[');
    }

    public function endArray(): void
    {
        $this->clearLastCommaOccurrence();

        fwrite($this->stream, ']');
    }

    public function key(string $key): void
    {
        $this->keyHasBeenWritten = true;
        fwrite($this->stream, "\"$key\":");
        $this->lastKey = $key;
    }

    public function value($value)
    {
        if (is_int($value)) {
            fwrite($this->stream, $value);
        } elseif (is_bool($value)) {
            fwrite($this->stream, $value ? 'true' : 'false');
        } elseif (is_null($value)) {
            fwrite($this->stream, 'null');
        } else {
            fwrite($this->stream, "\"$value\"");
        }

        call_user_func($this->callback, $this->stream, $this->lastKey, $value);

        fwrite($this->stream, ',');
    }

    public function startDocument(): void
    {
        //
    }

    public function whitespace(string $whitespace): void
    {
        //
    }

    private function clearLastCommaOccurrence()
    {
        if ($this->keyHasBeenWritten) {
            fseek($this->stream, -1, SEEK_END);
            $this->keyHasBeenWritten = false;
        }

        fseek($this->stream, -1, SEEK_END);

        if (fread($this->stream, 1) === ',') {
            fseek($this->stream, -1, SEEK_END);
        }
    }
}

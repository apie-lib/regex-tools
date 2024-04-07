<?php
namespace Apie\RegexTools;

use Apie\RegexTools\Parts\RegexPartInterface;
use Iterator;

final class RegexPartIterator implements Iterator
{
    private int $counter = 0;

    private ?RegexPartInterface $currentPart = null;

    private ?RegexStream $stream = null;

    public function __construct(private readonly string $input)
    {
    }

    public function current(): ?RegexPartInterface
    {
        return $this->currentPart;
    }
    public function key(): int
    {
        return $this->counter;
    }
    public function next(): void
    {
        $this->counter++;
        $this->currentPart = $this->stream->nextToken();
    }
    public function rewind(): void
    {
        $this->counter = 0;
        $this->stream = new RegexStream($this->input);
        $this->currentPart = $this->stream->nextToken();
    }
    public function valid(): bool
    {
        return null !== $this->stream && null !== $this->currentPart;
    }
}

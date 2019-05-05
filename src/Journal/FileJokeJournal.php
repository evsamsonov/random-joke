<?php declare(strict_types=1);

namespace App\Journal;

use App\Entity\Joke;
use App\Exception\JokeJournalException;
use Symfony\Component\Filesystem\Filesystem;

class FileJokeJournal implements JokeJournalInterface
{
    /** @var string */
    protected $filePath;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * FileRandomJokeJournal constructor.
     * @param string $filePath
     * @param Filesystem $filesystem
     */
    public function __construct(string $filePath, Filesystem $filesystem)
    {
        $this->filePath = $filePath;
    }

    /**
     * Добавить шутку в журнал
     * @param Joke $joke
     */
    public function add(Joke $joke): void
    {
        $dir = dirname($this->filePath);
        if ( ! is_dir($dir)) {
            $this->filesystem->mkdir($dir);
        }

        if ( ! is_writable($dir)) {
            throw new JokeJournalException(sprintf('Запись в директорию %s невозможна', $dir));
        }

        if (false === @file_put_contents($this->filePath, $joke->getText() . PHP_EOL, FILE_APPEND | LOCK_EX)) {
            throw new JokeJournalException(sprintf('Ошибка при записи файла %s', $this->filePath));
        }
    }
}
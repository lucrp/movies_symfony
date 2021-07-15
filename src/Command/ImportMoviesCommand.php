<?php

namespace App\Command;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\MovieImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:import-movies',
    description: 'Import movies from XML file',
)]
class ImportMoviesCommand extends Command
{
    private EntityManagerInterface $em;
    private SymfonyStyle $io;

    protected MovieImporter $importer;
    /**
     * @var MovieRepository
     */
    private $moviesRep;
    /**
     * @var string
     */
    private string $dataDirectory;

    public function __construct(
        EntityManagerInterface $em,
        MovieImporter $importer,
        string $dataDirectory,
        MovieRepository $moviesRep
        )
    {
        parent::__construct();
        $this->em = $em;
        $this->dataDirectory = $dataDirectory;
        $this->importer = $importer;
        $this->moviesRep = $moviesRep;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importMovies();

        return Command::SUCCESS;
    }


    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory . 'movies.xml';

        $fileExtensions = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [
            new ObjectNormalizer()
            ];

        $encoders = [
            new XmlEncoder()
            ];

        $serializer = new Serializer($normalizers, $encoders);

        /** @var string $fileString */
        $fileString = file_get_contents($file);

        return $serializer->decode($fileString, $fileExtensions);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function importMovies(): void
    {
        $this->io->section('Import movies from XML file');
        $importedMovies = 0;

        foreach ($this->getDataFromFile() as $row) {
            if (array_key_exists('id', $row) && !empty($row['id'])) {
                $movie = $this->moviesRep->findOneBy([
                    'id' => $row['id']
                ]);

                if ($movie) {
                    $movie = new Movie();
                    $movie->setTitle($row['title'])
                        ->setGenre($row['genre'])
                        ->setDescription($row['description'])
                        ->setRate($row['rate'])
                        ->setRuntime($row['runtime'])
                        ->setYear($row['year']);

                    $this->em->persist($movie);
                    $importedMovies++;
                }
            }
        }

        $this->em->flush();

        if ($importedMovies > 1) {
            $string = "{$importedMovies} MOVIES IMPORTED TO DATABASE ";
        } elseif ($importedMovies === 1) {
            $string = "1 MOVIE IMPORTED TO DATABASE ";
        } else {
            $string = 'NO MOVIES WERE IMPORTED';
        }
        $this->io->success($string);
    }
}

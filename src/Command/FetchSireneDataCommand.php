<?php
namespace App\Command;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;
use DateTimeImmutable;

class FetchSireneDataCommand extends Command
{
    protected static $defaultName = 'app:fetch-sirene-data';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch Sirene data.')
            ->setHelp('This command allows you to Fetch Sirene data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://files.data.gouv.fr/sirene/sirene_2018088_E_Q.zip';
        $destination_dir = 'var/tmp/';
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0755, true);
        }

        $local_zip_file = basename(parse_url($url, PHP_URL_PATH));
        if (!copy($url, $destination_dir . $local_zip_file)) {
            $output->writeln('<fg=red>Failed to copy Zip from ' . $url . ' to ' . ($destination_dir . $local_zip_file) . '</>');
            die();
        }

        $zip = new ZipArchive();
        if ($zip->open($destination_dir . $local_zip_file)) {
            $local_file_name = $zip->getNameIndex(0);
            if ($zip->extractTo($destination_dir, array($local_file_name))) {
                $output->writeln('File extracted from ' . $url . ' to ' . $destination_dir . $local_file_name);
            }
            $zip->close();
            unlink($destination_dir . $local_zip_file);
        } else {
            $output->writeln('<fg=red>Failed to extract file from Zip</>');
            unlink($destination_dir . $local_zip_file);
            die();
        }

        $file = fopen($destination_dir . $local_file_name, 'r');

        $loopIndex = 1;
        $firstLine = true;
        while (($line = fgetcsv($file, 0, ';')) !== FALSE) {
            if ($firstLine) {
                // Skip the first line of the csv file (columns titles)
                $firstLine = false;
                continue;
            }

            $company = new Company();
            $company->setSiren($line[0]);
            $company->setName(utf8_decode($line[60]));
            $company->setDateMAJ(new DateTimeImmutable($line[99]));

            $this->entityManager->persist($company);
            unset($company);

            if ($loopIndex % 1000 === 0) {
                $this->entityManager->flush();
            }
            $loopIndex++;
        }

        $this->entityManager->flush();
        fclose($file);
        unlink($destination_dir . $local_file_name);

        $output->writeln('<fg=green>' . ($loopIndex - 1) . ' entries have been successfully added into the DB.</>');

        return 0;
    }
}
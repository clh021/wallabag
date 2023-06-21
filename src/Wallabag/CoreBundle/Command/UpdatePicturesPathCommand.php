<?php

namespace Wallabag\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wallabag\CoreBundle\Repository\EntryRepository;

class UpdatePicturesPathCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private EntryRepository $entryRepository;
    private string $wallabagUrl;

    public function __construct(EntityManagerInterface $entityManager, EntryRepository $entryRepository, $wallabagUrl)
    {
        $this->entityManager = $entityManager;
        $this->entryRepository = $entryRepository;
        $this->wallabagUrl = $wallabagUrl;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('wallabag:update-pictures-path')
            ->setDescription('Update the path of the pictures for each entry when you changed your wallabag instance URL.')
            ->addArgument(
                'old-url',
                InputArgument::REQUIRED,
                'URL to replace'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $oldUrl = $input->getArgument('old-url');

        $entries = $this->entryRepository->findAll();
        $io->text('Retrieve existing entries');
        $i = 1;
        foreach ($entries as $entry) {
            $content = str_replace($oldUrl, $this->wallabagUrl, $entry->getContent());
            $previewPicture = str_replace($oldUrl, $this->wallabagUrl, $entry->getPreviewPicture());
            $entry->setContent($content);
            $entry->setPreviewPicture($previewPicture);
            $this->entityManager->persist($entry);

            if (0 === ($i % 20)) {
                $this->entityManager->flush();
            }
            ++$i;
        }
        $this->entityManager->flush();

        $io->success('Finished updating.');

        return 0;
    }
}

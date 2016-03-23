<?php
namespace Arkade\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigListCommand extends \Symfony\Component\Console\Command\Command
{
    private $configFactory;

    private $log;

    public function __construct(
        \Magento\Framework\App\State $state,
        Factory $configFactory,
        LoggerInterface $log
    ) {
        $state->setAreaCode('adminhtml');
        $this->configFactory = $configFactory;
        $this->log = $log;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:config:list')
            ->setDescription('Lists whatever credentials for S3 you have provided for Magento.');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->configFactory->create();
        $output->writeln('Here are your AWS credentials.');
        $output->writeln('');
        $output->writeln(sprintf('Access Key ID:     %s', $config->getConfigDataValue('arkade_s3/general/access_key')));
        $output->writeln(sprintf('Secret Access Key: %s', $config->getConfigDataValue('arkade_s3/general/secret_key')));
        $output->writeln(sprintf('Bucket:            %s', $config->getConfigDataValue('arkade_s3/general/bucket')));
        $output->writeln(sprintf('Region:            %s', $config->getConfigDataValue('arkade_s3/general/region')));
    }
}

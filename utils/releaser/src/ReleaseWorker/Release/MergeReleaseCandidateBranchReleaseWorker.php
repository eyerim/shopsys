<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\Release;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class MergeReleaseCandidateBranchReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return sprintf(
            '[Manually] Merge "%s" branch into "%s"',
            $this->createBranchName($version),
            $this->initialBranchName
        );
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('You need to create a merge commit locally.');
        $this->symfonyStyle->warning(sprintf(
            'Do not forget to push the "%s" branch!',
            $this->initialBranchName
        ));
        $this->confirm(
            sprintf(
                'Confirm "%s" branch was merged and pushed to "%s"',
                $this->createBranchName($version),
                $this->initialBranchName
            )
        );

        if ($this->initialBranchName === 'master') {
            $this->symfonyStyle->note(
                'Rest assured, after the master branch is built on Heimdall, it is split automatically (using http://heimdall:8080/view/Tools/job/tool-monorepo-split/)'
            );
        } else {
            $this->symfonyStyle->note(
                sprintf(
                    'You need split the "%s" branch it using "tool-monorepo-force-split-branch" on Heimdall (http://heimdall:8080/view/Tools/job/tool-monorepo-force-split-branch/)',
                    $this->initialBranchName
                )
            );
        }
        $this->confirm('Confirm the branch is split.');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE;
    }
}

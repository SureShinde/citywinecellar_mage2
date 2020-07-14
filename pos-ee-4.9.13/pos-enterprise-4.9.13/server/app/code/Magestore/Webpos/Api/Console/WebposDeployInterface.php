<?php

namespace Magestore\Webpos\Api\Console;

interface WebposDeployInterface {

    /**
     * deploy webpos
     *
     * @return null
     */
    public function webposDeploy($deployName, $output);

}
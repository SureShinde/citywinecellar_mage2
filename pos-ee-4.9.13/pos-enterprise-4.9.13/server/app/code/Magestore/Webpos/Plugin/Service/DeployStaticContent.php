<?php
namespace Magestore\Webpos\Plugin\Service;

class DeployStaticContent
{

    /**
     * @var \Magestore\Webpos\Model\Deploy\DeployService
     */
    protected $deployService;

    /**
     * DeployStaticContent constructor.
     * @param \Magestore\Webpos\Model\Deploy\DeployService $deployService
     */
    public function __construct(
        \Magestore\Webpos\Model\Deploy\DeployService $deployService
    )
    {
        $this->deployService = $deployService;
    }

    public function afterDeploy(\Magento\Deploy\Service\DeployStaticContent $subject, $result)
    {
        /** deploy webapp */
        try {
            $this->deployService->execute();
        }catch(\Exception $e){
            throw new \Exception('can not deploy pos app, permission denied!');
        }
        return $result;
    }
}
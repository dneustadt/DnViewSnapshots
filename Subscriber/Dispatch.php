<?php

namespace DnViewSnapshots\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Dispatch
 * @package DnViewSnapshots\Subscriber
 */
class Dispatch implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Dispatch constructor.
     * @param $pluginDirectory
     * @param \Enlight_Template_Manager $templateManager
     * @param Container $container
     * @param Connection $connection
     */
    public function __construct(
        $pluginDirectory,
        \Enlight_Template_Manager $templateManager,
        Container $container,
        Connection $connection
    )
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->templateManager = $templateManager;
        $this->container = $container;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
            'Enlight_Controller_Action_PostDispatchSecure' => 'onPostDispatchSecure',
        ];
    }

    public function onPreDispatch()
    {
        $this->templateManager->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecure(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $request = $args->getSubject()->Request();

        if (strtolower($request->getModuleName()) !== 'frontend') {
            return;
        }

        $params = $request->getParams();
        $sessionID = $this->container->get('session')->get('sessionId');

        $isSessionRecorded = strtolower($request->getControllerName()) !== 'snapshots' ?
            $this->container->get('session')->get('isSessionRecorded') :
            false;

        $snapshotSessionID = $view->getAssign('snapshotSessionID');

        $view->assign(
            [
                'snapshotSessionID' => $snapshotSessionID ? : $sessionID,
                'isSessionRecorded' => $isSessionRecorded,
            ]
        );

        if (
            $snapshotSessionID ||
            !$isSessionRecorded ||
            $request->isXmlHttpRequest()
        )
        {
            return;
        }

        $template = $view->Template()->template_resource;

        $variables = $view->getAssign();

        array_walk_recursive($variables, function (&$value) {
            if (is_object($value)) {
                try {
                    // workaround for PDOException when trying to serialize PDO instances
                    serialize($value);
                }
                catch (\Throwable $e) {
                    // as we only need a snapshot for the view, remove the PDO instance
                    $value = null;
                }
            }
        });

        $variables = serialize($variables);

        $params['__controller'] = $request->getControllerName();
        $params['__action'] = $request->getActionName();
        $params = json_encode($params);

        $step = (int)$this->connection->fetchColumn(
            'SELECT MAX(`step`) FROM `view_snapshots` WHERE `sessionID` = :sessionID',
            ['sessionID' => $sessionID]
        );
        $step++;

        $this->connection->insert(
            'view_snapshots',
            [
                'sessionID' => $sessionID,
                'template' => $template,
                'variables' => $variables,
                'params' => $params,
                'step' => $step,
            ]
        );
    }
}
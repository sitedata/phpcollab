<?php


namespace phpCollab\Support;

use Exception;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;

/**
 * Class Support
 * @package phpCollab\Support
 */
class Support
{
    protected $support_gateway;
    protected $members;
    protected $teams;
    protected $db;
    protected $strings;
    protected $root;
    protected $requestStatus;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var Logger|null
     */
    private $logger;

    /**
     * Support constructor.
     * @param Database $database
     * @param Container $container
     * @param Logger|null $logger
     * @throws Exception
     */
    public function __construct(Database $database, Container $container, Logger $logger)
    {
        $this->db = $database;
        $this->container = $container;
        $this->logger = $logger;
        $this->support_gateway = new SupportGateway($this->db);
        $this->members = $container->getMembersLoader();
        $this->teams = $container->getTeams();
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
        $this->requestStatus = $GLOBALS["requestStatus"];
    }

    /**
     * @param int $supportRequestId
     * @return mixed
     */
    public function getSupportRequestById(int $supportRequestId)
    {
        return $this->support_gateway->getSupportRequestById($supportRequestId);
    }

    /**
     * @param int $status
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatus(int $status, string $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatus($status, $sorting);
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByProject(int $projectId, string $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByProject($projectId, $sorting);
    }

    /**
     * @param int $memberId
     * @return mixed
     */
    public function getSupportRequestByMemberId(int $memberId)
    {
        return $this->support_gateway->getSupportRequestByMemberId($memberId);
    }

    /**
     * @param int $supportRequestId
     * @return mixed
     */
    public function getSupportRequestByIdIn(int $supportRequestId)
    {
        return $this->support_gateway->getSupportRequestByIdIn($supportRequestId);
    }

    /**
     * @param Int $requestStatus
     * @param Int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId(int $requestStatus, int $projectId, string $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatusAndProjectId($requestStatus, $projectId, $sorting);
    }

    /**
     * @param int $memberId
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByMemberIdAndProjectId(int $memberId, int $projectId, string $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByMemberIdAndProjectId($memberId, $projectId, $sorting);
    }

    /**
     * @param int $requestId
     * @return mixed
     */
    public function getSupportPostsByRequestId(int $requestId)
    {
        return $this->support_gateway->getSupportPostsByRequestId($requestId);
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function getSupportPostById(int $postId)
    {
        return $this->support_gateway->getSupportPostById($postId);
    }

    /**
     * @param int $postIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn(int $postIds)
    {
        return $this->support_gateway->getSupportPostsByRequestIdIn($postIds);
    }

    /**
     * @param int $userId
     * @param int $priority
     * @param string $subject
     * @param string $message
     * @param int $project
     * @param int $status
     * @return string
     */
    public function addSupportRequest(int $userId, int $priority, string $subject, string $message, int $project, int $status = 0): string
    {
        return $this->support_gateway->createSupportRequest($userId, $priority, $subject, $message, $project, $status);
    }

    /**
     * @param int $requestId
     * @param string $message
     * @param string $dateCreated
     * @param int $ownerId
     * @param int $projectId
     * @return string
     */
    public function addSupportPost(int $requestId, string $message, string $dateCreated, int $ownerId, int $projectId): string
    {
        $newPostId = $this->support_gateway->addPost($requestId, $message, $dateCreated, $ownerId, $projectId);
        return $this->getSupportPostById($newPostId);
    }

    /**
     * @param int $requestId
     * @param int $status
     * @param string|null $dateClose
     * @return mixed
     */
    public function updateSupportPostStatus(int $requestId, int $status, string $dateClose = null)
    {
        return $this->support_gateway->updateSupportRequest($requestId, $status, $dateClose);
    }

    /**
     * @param string $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests(string $supportRequestIds)
    {
        return $this->support_gateway->deleteSupportRequests($supportRequestIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteSupportRequestsByProjectId(string $projectIds)
    {
        return $this->support_gateway->deleteSupportRequestsByProjectId($projectIds);
    }

    /**
     * @param string $requestIds
     * @return mixed
     */
    public function deleteSupportPostsByRequestId(string $requestIds)
    {
        return $this->support_gateway->deleteSupportPostsByRequestId($requestIds);
    }

    /**
     * @param string $supportPostIds
     * @return mixed
     */
    public function deleteSupportPostsById(string $supportPostIds)
    {
        return $this->support_gateway->deleteSupportPostsById($supportPostIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteSupportPostsByProjectId(string $projectIds)
    {
        return $this->support_gateway->deleteSupportPostsByProjectId($projectIds);
    }

    /**
     * @param array $postDetails
     * @return void
     * @throws Exception
     */
    public function sendPostChangedNotification(array $postDetails)
    {
        // Gather the needed information for populating the email template
        $requestDetail = $this->getSupportRequestById($postDetails["sp_request_id"]);
        $userDetail = $this->members->getMemberById($requestDetail["sr_member"]);
        $teamMembers = $this->teams->getTeamByProjectId($postDetails["sp_project"]);


        $mail = $this->container->getNotification();

        $emailSubject = $this->strings["support"] . " " . $this->strings["support_id"] . ": " . $requestDetail["sr_id"];

        $emailMessage = <<<EMAIL_MESSAGE
{$this->strings["noti_support_status2"]}

{$this->strings["id"]} : {$requestDetail["sr_id"]}
{$this->strings["subject"]} : {$requestDetail["sr_subject"]}
{$this->strings["status"]} : {$this->requestStatus[$requestDetail["sr_status"]]}
{$this->strings["details"]} : 

EMAIL_MESSAGE;

        $from = [
            'email' => $userDetail["mem_email_work"],
            'name' => $userDetail["mem_name"]
        ];

        try {
            // We want to send a notification to all team members so everyone is informed and anyone can respond
            // if needed.
            foreach ($teamMembers as $teamMember) {
                // If there is no email address, then skip it
                if ($teamMember["tea_mem_email_work"]) {

                    $to = [
                        'email' => $teamMember["tea_mem_email_work"],
                        'name' => $teamMember["tea_mem_name"]
                    ];

                    if ($teamMember["tea_mem_profil"] == 3) {
                        $emailMessage .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $postDetails["sp_project"] . "\n\n";
                    } else {
                        $emailMessage .= "$this->root/general/login.php?url=support/viewrequest.php%3Fid={$requestDetail["sr_id"]}\n\n";
                    }

                    $mail->sendMessage($to, $from, $emailSubject, $emailMessage);
                }
            }
        } catch (Exception $e) {
            $this->logger->error("Support Class error: sendPostChangedNotification - " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Notifications Section
     * @param array $requestDetail
     * @param array $postDetails
     * @param array $userDetails
     * @throws Exception
     */
    public function sendNewPostNotification(array $requestDetail, array $postDetails, array $userDetails)
    {
        if (
            $requestDetail
            && $postDetails
            && $userDetails
            && !empty($userDetails["mem_email_work"])
        ) {
            $mail = $this->container->getNotification();
            try {

                // Set the From field
                $mail->setFrom($userDetails["mem_email_work"], $userDetails["mem_name"]);

                // Start building the Subject and Body
                $mail->partSubject = $this->strings["support"] . " " . $this->strings["support_id"];
                $mail->partMessage = $this->strings["noti_support_post2"];


                // Set the subject
                $subject = $mail->partSubject . ": " . $requestDetail["sr_id"];

                // Build the Email Body
                $body = <<<MAILBODY
$mail->partMessage

{$this->strings["id"]} : {$requestDetail["sr_id"]}
{$this->strings["subject"]} : {$requestDetail["sr_subject"]}
{$this->strings["status"]} : {$GLOBALS["requestStatus"][$requestDetail["sr_status"]]}

{$this->strings["details"]} : 

MAILBODY;

                if (isset($listTeam) && $listTeam["tea_mem_profil"] == 3) {
                    $body .= $this->root . "/general/login.php?url=projects_site/home.php%3Fproject=" . $requestDetail["sr_project"] . "\n\n";
                } else {
                    $body .= $this->root . "/general/login.php?url=support/viewrequest.php%3Fid=" . $requestDetail["sr_id"] . "\n\n";
                }
                $body .= $this->strings["message"] . " : " . $postDetails["sp_message"] . "";


                $body .= "\n\n" . $mail->footer;

                $mail->Subject = $subject;
                $mail->Priority = "3";
                $mail->Body = $body;
                $mail->AddAddress($userDetails["mem_email_work"], $userDetails["mem_name"]);
                $mail->Send();
                $mail->ClearAddresses();


            } catch (Exception $e) {
                $this->logger->error("Support Class error: sendNewPostNotification - " . $e->getMessage());
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            $this->logger->error("Support Class error: sendNewPostNotification - Error sending mail");
            throw new Exception('Error sending mail');
        }

    }
}

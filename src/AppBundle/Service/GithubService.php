<?php
/**
 * Github Service
 */

namespace AppBundle\Service;

use Github\Client;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GithubService
{
    /** @var array $config */
    protected $config;

    /** @var Client GitHub github */
    protected $github;

    /** @var RequestStack */
    protected $requestStack;

    /** @var SessionInterface $session */
    protected $session;

    const GH_AUTH_URL = 'https://github.com/login/oauth/authorize';
    const GH_ACCESS_URL = 'https://github.com/login/oauth/access_token';
    const SESSION_STATE = 'github_session_state';

    /**
     * @param array $config
     * @param RequestStack $requestStack
     * @param SessionInterface $session
     */
    public function __construct(array $config, RequestStack $requestStack, SessionInterface $session)
    {
        $this->config = $config;
        $this->github = new Client();
        $this->requestStack = $requestStack;
        $this->session = $session;
    }

    /**
     * @return string Auth URL
     */
    public function getAuthUrl()
    {
        $state = substr(md5(time()), 0, 32);
        $this->session->set(GithubService::SESSION_STATE, $state);

        return self::GH_AUTH_URL . '?client_id=' . $this->config['client_id'];
    }

    public function getClient()
    {
        return $this->github;
    }
}

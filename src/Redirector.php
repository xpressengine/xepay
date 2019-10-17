<?php
namespace Xehub\Xepay;

use Illuminate\Routing\Redirector as LaravelRedirector;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirector
{
    protected $redirector;

    protected $redirects = [];

    public function __construct(LaravelRedirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     * @param Order $order
     * @return RedirectResponse
     */
    public function redirectToComplete(Order $order)
    {
        $response = isset($this->redirects['complete']) ?
            call_user_func($this->redirects['complete'], $order) :
            '/';

        return $this->redirectable($response);
    }

    /**
     * @param Order $order
     * @return RedirectResponse|null
     */
    public function redirectToFail(Order $order)
    {
        if (!isset($this->redirects['fail'])) {
            return null;
        }

        $response = call_user_func($this->redirects['fail'], $order);

        return $this->redirectable($response);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function completing($callback)
    {
        $this->redirects['complete'] = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function failing($callback)
    {
        $this->redirects['fail'] = $callback;

        return $this;
    }

    protected function redirectable($response)
    {
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        return $this->redirector->to($response);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class IrmaAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = Session::get('irma_session_token', '');
        if ($request->route()->getName() === 'irma_auth.start') {
            //start session
            $response = response($this->start($request));
            if ($response) {
                return response($this->start($request));
            } else {
                return redirect()->route('irma_session.authenticate', urlencode(urlencode(\URL::to('/').'/'.$request->path())));
            }
        } elseif ($token === '') {
            //echo \URL::to('/').'/'.$request->path();
            //return \URL::to('/').'/'.$request->path();
            return redirect()->route('irma_session.authenticate', urlencode(urlencode(\URL::to('/').'/'.$request->path())));
        } else {
            //verify
            $result = $this->irma_get_session_result($token);
            if ($result && $result->proofStatus == 'VALID') {
                $validated_email = $result->disclosed[0][0]->rawvalue;
                session(['validated_email' => $validated_email]);
            } else {
                //empty token to try again
                session(['irma_session_token' => '']);
                return redirect()->route('irma_session.authenticate', urlencode(urlencode(\URL::to('/').'/'.$request->path())));
            }
        }
        return $next($request);
    }

    private function start()
    {
        $irmasession = $this->irma_start_session([
            '@context' => 'https://irma.app/ld/request/disclosure/v2',
            'disclose' => [
                [
                    ['pbdf.pbdf.email.email'],
                ],
            ],
        ]);

        if ($irmasession) {
            session(['irma_session_token' => $irmasession->token]);
            return json_encode($irmasession->sessionPtr);
        } else {
            return json_encode(false);
        }
    }

    private function irma_server_call($method, $suburl, $payload = null)
    {
        if (!isset($payload)) {
            $api_call = array(
                'http' => array(
                    'method' => $method,
                    'header' => "Authorization: " . env('IRMA_API_TOKEN') . "\r\n",
                ),
            );
        } else {
            $json_payload = json_encode($payload);

            $api_call = array(
                'http' => array(
                    'method' => $method,
                    'header' => "Content-Type: application/json\r\n"
                    . "Content-Length: " . strlen($json_payload) . "\r\n"
                    . "Authorization: " . env('IRMA_API_TOKEN') ."\r\n",
                    'content' => $json_payload,
                ),
            );
        }

        $url = env("IRMA_SERVER_URL") . $suburl;
        $file_headers = @get_headers($url);
        if (!$file_headers || ($file_headers[0] == 'HTTP/1.0 404 Not Foundx') || $file_headers[0] == ('HTTP/1.0 400 Bad Request') || ($file_headers[0] == 'HTTP/1.1 404 Not Foundx') || ($file_headers[0] == 'HTTP/1.1 400 Bad Request')) {
            $response_data = false;
        } else {
            $response = file_get_contents($url, false, stream_context_create($api_call));
            if (!$response) {
                Log::error(sprintf('No response data from IRMA server %s', $url));
                http_response_code(501);
                echo 'Internal server error';
                exit();
            }

            $response_data = json_decode($response);
            if ($response_data === null) {
                Log::error(sprintf('Empty response data from IRMA server %s', $url));
                http_response_code(502);
                echo 'Internal server error';
                exit();
            }
        }

        return $response_data;
    }

    private function irma_start_session($request)
    {
        return $this->irma_server_call('POST', '/session', $request);
    }

    private function irma_get_session_result($token)
    {
        return $this->irma_server_call('GET', '/session/' . $token . '/result');
    }
}

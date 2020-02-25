<?php

namespace app\classes\controllers;

use classes\mvc\Controller;
use classes\routage\Request;
use classes\routage\Response;
use app\classes\services\Test3;
use traits\DIS;

class Test extends Controller {
    use DIS;

    private ?Test3 $test3 = null;

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     *
     * @http get
     * @route /:lol
     */
    public function toto(Request $req, Response $res): void {
        echo '<pre>';
        var_dump($req, $res, 'param lol => ', $req->param('lol'), 'get[test] => ', $req->get('test'), '$this->test3->toto()');
        $this->test3->toto();
        echo '</pre>';
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @route /test/titi/:lol
     */
    public function tata(Request $req, Response $res): string {
        $res->assign('test', 'name');
        return $res->json([
            '{test}' => '{test}',
            'param lol => ' => $req->param('lol'),
            'get[test] => ' => $req->get('test')
        ]);
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @route /montest/test.html
     */
    public function test(Request $req, Response $res): string {
        return $res->html('
<script>
        function post(element) {
            fetch("/toto/toto/tata", {
                method: "post",
                headers: {
                    "Content-Type": element.getAttribute("data-type")
                },
                body: element.getAttribute("data-send")
            }).then(r => r.text())
            .then(html => {
                document.querySelector("div").innerHTML = html;
            })
        }
</script>
<button onclick="post(this)" data-type="application/json" data-send=\'{"coucou": "Nico"}\'>POST JSON</button>
<button onclick="post(this)" data-type="text/html" data-send="<html><title>coucou</title><b>test d\'envois HTML</b></html>">POST HTML</button>
<p>Retour: </p>
<div>
    <b>Rien</b>
</div>');
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @http post
     * @route /toto/tata
     */
    public function titi(Request $req, Response $res): string {
        if(!is_null($req->post('html')))
            return $res->html($req->post('html'));
        return $res->json($req->post());
    }
}
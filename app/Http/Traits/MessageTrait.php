<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;

trait MessageTrait{
    public function msgInclude($object)
    {
        return response()->json([
            ['message' => 'Registro incluso com sucesso.'],
            $object
        ], 201);
    }

    public function msgUpdated(&$object)
    {
        return response()->json([
            ['message' => 'Registro atualizado com sucesso.'],
            $object
        ], 201);
    }

    public function msgDuplicatedField(string $fieldName = null, &$object = null)
    {
        return response()->json([
            ['message' => "Gravação cancelada. O valor informado para '{$fieldName}' está em duplicidade com outro registro.."],
            $object
        ], 401);
    }

    public function msgDuplicatedFieldIgnored(string $fieldName = null, &$object = null)
    {
        return response()->json([
            ['message' => "CAMPO NÃO ATUALIZADO. O valor informado para '{$fieldName}' está em duplicidade com outro registro.."],
            $object
        ], 201);
    }


    public function msgDuplicatedIgnoredField(string $fieldName = null, &$object = null)
    {
        return response()->json([
            ['message' => "Registro atualizado com sucesso com exeção de '{$fieldName}' pois está em duplicidade em um campo chave"],
            $object
        ], 201);
    }

    public function msgDuplicated(&$object = null)
    {
        return response()->json([
            ['message' => "A combinação informada é inválida pois está em duplicidade."],
            $object
        ], 401);
    }

    public function msgMissingValidator(&$validator = null)
    {
        return response()->json([
            ['message' => "Algum campo de preenchimento obrigatório está faltando ou está com preenchimento inválido."],
            $validator->errors()
        ], 406);
    }

    public function msgInvalidValue(&$validator = null)
    {
        return response()->json([
            ['message' => "O Valor informado é inválido."],
            $validator->errors()
        ], 406);
    }


    public function msgMissingRequest(Request &$request = null)
    {
        return response()->json([
            ['message' => "Algum campo de preenchimento obrigatório está faltando."],
            $request
        ], 401);
    }

    public function msgMissingField(string $fieldName = null, &$validator = null)
    {
        $message = "O campo '{$fieldName}' não foi informado.";
        $messageNofield = "O campo '{$fieldName}' não foi informado.";
        if (is_null($validator))
            return response()->json(['message' => $message], 400);
        elseif (is_null($fieldName))
            return response()->json([
                ['message' => $messageNofield],
                $validator->errors()
            ], 401);
        else
            return response()->json([
                ['message' => $message],
                $validator->errors()
            ], 401);
    }

    public function msgNoParameterInformed()
    {
        return response()->json([
            'message' => "Nenhum parametro informado para atualizar.",
        ], 401);
    }

    public function msgRecordNotFound()
    {
        return response()->json([
            'message' => "Registro não encontrado.",
        ], 401);
    }

    public function msgRecordNameNotFound(string $name)
    {
        return response()->json([
            'message' => "{$name} não encontrado.",
        ], 401);
    }

    public function msgRecordDisabled(&$object = null)
    {
        return response()->json([
            ['message' => "Registro foi desativado."],
            $object
        ], 201);

    }

    public function msgRecordDeleted(&$object = null)
    {
        return response()->json([
            ['message' => "Registro foi excluído."],
            $object
        ], 201);

    }

    public function msgRecordExceptionNotDelete(&$object = null)
    {
        return response()->json([
            ['message' => 'Não é possível excluir esse registro porque existem registros dependentes em outras tabelas.'],
            $object
        ], 401);
    }

    public function msgNotAuthorized()
    {
        return response()->json([
            'message' => 'Recurso não autorizado.'
        ], 401);
    }

    public function msgRouteNotFound(string $route = '')
    {
        return response()->json([
            'message' => "'{$route}' é um caminho ou nome de rota inválida."
        ], 401);
    }

    public function msgGisTokenNotFound()
    {
        return response()->json([
            'message' => 'GIS_TOKEN está faltando no arquivo .env'
        ], 401);
    }

    public function msgGisProdApi()
    {
        return response()->json([
            'message' => 'PROD_API está faltando no arquivo .env'
        ], 401);
    }

    public function msgGisCheckProdDevApi()
    {
        return response()->json([
            'message' => 'PROD_FIN_API está faltando no arquivo .env'
        ], 401);
    }

    public function msgEnv(string $tagEnv = '')
    {
        return response()->json([
            'message' => "{$tagEnv} está faltando no arquivo .env"
        ], 401);
    }

    public function msgErrorExcept(string $e = '')
    {
        return response()->json([
            'message' => "{$e}"
        ], 401);
    }

    public function msgSuccess(string $msg = '')
    {
        return response()->json([
            'message' => "{$msg}"
        ], 201);
    }

    public function msgInvalidToken()
    {
        return response()->json([
            'message' => 'Token inválido.',
        ], 400);
    }
}

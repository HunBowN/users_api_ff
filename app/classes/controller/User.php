<?php
class Controller_User extends Controller
{
    public function index()
    {
        $this->title = 'Пользователи';

        return $this->render('pages/users', []);
    }

    public function getUsers()
    {
        $startCommon = microtime(true);

        $usersPerLoop = 5000;
        $loopCount = 4;
        $chunkSize = 1000;
        $usersCount = $loopCount * $usersPerLoop;

        $logger = new Logger(FROOT . '/storage/logs/get_users.log');
        $logger->log("__Запрос на получение $usersCount пользователей__");
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '240');

        try {
            $users = [];
            for ($i = 0; $i < $loopCount; $i++) {
                $step = $i + 1;
                $start = microtime(true);
                $data = Request::makeCurlRequest('https://randomuser.me/api/', ['results' => $usersPerLoop]);
                $diff = sprintf('%.6f sec.', microtime(true) - $start);
                $logger->log("StepFetch $step| Получено $usersPerLoop  пользователей, время $diff");
                $result = $data['results'];
                $users = array_map(function ($item) {
                    return $this->parseUserData($item); }, $result);
                $usersChanks = array_chunk($users, $chunkSize);
                $user = Model::factory('User');

                foreach ($usersChanks as $key => $chunk) {
                    $start = microtime(true);
                    $transaction = $user->insertMultiple($chunk);
                    $diff = sprintf('%.6f sec.', microtime(true) - $start);
                    if ($transaction['error'])
                        throw new Exception($transaction['message']);
                    $step = $key + 1;
                    $logger->log("StepInsert $step|  Всего добавлено в таблицу  " . $step * $chunkSize . "  пользователей, время $diff");
                }

            }

            $diff = sprintf('%.6f sec.', microtime(true) - $startCommon);
            $logger->successLog("Все $usersCount пользователей добавлены в таблицу, время $diff");
            return $this->jsonResponse(['message' => "Inserted successfully $usersCount users"]);
        } catch (\Throwable $ex) {
            $error = $ex->getMessage();
            $code = $ex->getCode();
            $logger->errorLog('Ошибка транзакции ' . $error);
            return $this->jsonResponse(['error' => true, 'code' => $code, 'message' => $error]);
        }

    }


    protected function parseUserData(array $array, string $parentKey = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $parentKey = $key;
                $results = array_merge($results, $this->parseUserData($value, $parentKey));
            } else {
                if (!empty($parentKey))
                    $key = $parentKey . '_' . $key;
                $results[$key] = $value;
            }
        }

        return $results;
    }

    public function getRelatives()
    {
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;
        try {
            $relatives = Model::factory('User')->getRelatives($limit, $offset);
            return $this->jsonResponse(['code' => 200, 'error' => false, 'relatives' => $relatives]);
        } catch (\Exception $ex) {
            return $this->jsonResponse(['code' => 400, 'error' => true, 'message' => $ex->getMessage()]);
        }
    }
    public function getRelativesCount()
    {
        try {
            $relativesCount = Model::factory('User')->getRelativesCount();
            return $this->jsonResponse(['code' => 200, 'error' => false, 'relativesCount' => $relativesCount]);
        } catch (\Exception $ex) {
            return $this->jsonResponse(['code' => 400, 'error' => true, 'message' => $ex->getMessage()]);
        }
    }




    // public function createTabe() {
    //     $request = new Request();
    //     $users = $request->makeCurlRequest('https://randomuser.me/api/', ['results' => 1]);
    //     $users = $users['results'];

    //     $users = array_map( function ($item) { return $this->parseUserData($item); }, $users);

    //     $types = array_values( array_map(function ($item) {
    //         return gettype($item);
    //     }, $users[0]));
    //     try {
    //         $user = Model::factory('User');
    //         $user->insertMultiple($users);
    //         $user->createTable(array_keys($users[0]), $types);
    //         return $this->jsonResponse(['message' => 'success']);

    //     } catch (\Throwable $th) {
    //         return $this->jsonResponse(['message' => $th->getMessage()]);
    //     }

    // }   

}
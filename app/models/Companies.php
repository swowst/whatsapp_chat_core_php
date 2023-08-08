<?php
namespace Models;

use Custom\Models\Documents;
use Custom\Models\Notes;
use Fogito\Http\Response;
use Fogito\Lib\Company;
use Fogito\Models\CoreCompanies;
use Lib\Permission;

class Companies
{
    public static function getClasses()
    {
        return [
            "notes"  => new Notes()
        ];
    }

    public static function filterBranchIds($branchIds){

        $branchIds = Notes::filterMongoIds($branchIds);

        if (!Permission::check('branches_sharing') || count($branchIds) == 0){
            $branchIds = [Company::getId()];
        }

        return $branchIds;
    }

    public static function prepareData($company_ids){
        $preparedData = [];
        $branchesCount = count((array)$company_ids);
        if ($branchesCount > 0){
            $showCompanyIds = array_slice($company_ids,0,3);
            $branchesData = CoreCompanies::find([['id' => ['$in' => $showCompanyIds]]]);

            foreach ($branchesData as $branchData){
                $preparedData[] = [
                    'id' => $branchData['id'],
                    'title' => $branchData['title'],
                    'fullname' => $branchData['title'],
                    'avatar_custom' => (bool)$branchData['avatar'],
                    'avatar' => $branchData['avatar'] ? $branchData['avatar']['avatars']['tiny'] : false,
                ];
            }
        }

        return [
            "data" => $preparedData,
            "count" => $branchesCount
        ];
    }


    public static function findAndSet($list, $params=[])
    {
        $keyFrom    = $params["key_from"] ? : "branches";
        $keyTo      = $params["key_to"] ?  : "branches";
        $columns    = $params["columns"] && count($params["columns"]) > 0 ? $params["columns"]: ['id','title','avatar'];

        $ids = [];
        foreach ($list as $value)
            $ids = array_merge($ids,(array)$value[$keyFrom]);

        $ids = array_values(array_unique($ids));

        $dataById = [];
        if(count($ids) > 0) {
            $query = CoreCompanies::find([
                ["id" => ['$in' => $ids]],
                "columns" => $columns
            ]);
            foreach ($query as $value)
                $dataById[(string)$value["id"]] = $value;
        }

        $data = [];
        foreach ($list as $value)
        {
            $preparedData = [];
            $currentIds = (array)$value[$keyFrom];
            foreach ($currentIds as $compId){
                $branchData = $dataById[$compId];
                $preparedData[] = [
                    'id' => $branchData['id'],
                    'title' => $branchData['title'],
                    'fullname' => $branchData['title'],
                    'avatar_custom' => (bool)$branchData['avatar'],
                    'avatar' => $branchData['avatar'] ? $branchData['avatar']['avatars']['tiny'] : false,
                ];
            }

            $value[$keyTo] = [
                'data' => $preparedData,
                'count' => count($currentIds),
            ];

            $data[] = $value;
        }

        return $data;
    }

}

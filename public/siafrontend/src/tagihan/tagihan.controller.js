(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanController', TagihanController);

  TagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function TagihanController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Tagihan';
    vm.table = 'tagihan';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};
    vm.searchValue = false;
    vm.filterValue = false;
    vm.status = null;

    vm.search = search;

    activate();

    scope.$watch('vm.status', status_changes);

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    scope.$watch('vm.filterValue', function(newVal){
      if (vm.filterValue != null) {
        getData();
      }
    }, true);

    function activate() {
      vm.page = 1;
      getOption();
    }

    function getData() {
      return dataservice.getData(vm.table, vm.page, vm.searchValue, vm.filterValue).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function search() {
      vm.searchValue = {
        'field': 'orang',
        'value': angular.copy(vm.searchData)
      };
      if (vm.page == 1) {
        getData();
      }else{
        vm.page = 1;
      }
    }

    function status_changes(newVal) {
      if (newVal != null) {
        insertFilter('status', newVal);
      }
    }

    function insertFilter(field, val) {

      let status_value = {
        'field': field,
        'value': val
      };

      if (vm.filterValue) {
        let field_name = vm.filterValue.map(function(v, i){ return v.field });
        let array_index = field_name.indexOf(field);

        if (array_index != -1) {
          vm.filterValue[array_index]['value'] = val;
        }else{
          vm.filterValue.push(status_value);
        }
      }else{
        vm.filterValue = [status_value];
      }
    }

    scope.$watch('vm.searchData', function(newVal, oldVal) {
      if (newVal != undefined) {
        search();
      }
    });

  }
})();

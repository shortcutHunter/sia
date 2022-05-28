(function() {
  'use strict';

  angular
    .module('app.dosen_pjmk')
    .controller('DosenPjmkFormController', DosenPjmkFormController);

  DosenPjmkFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function DosenPjmkFormController($q, dataservice, logger, stateParams, scope) {
    var vm = this;
    vm.title = 'Form Dosen PJMK';
    vm.table = 'dosen_pjmk';
    vm.semester_ids = {};
    vm.data = {
      semester: []
    };

    scope.$watch(() => vm.semester_ids, (newVal, oldVal) => {
      if (newVal) {
        vm.data.semester = [];
        $.each(newVal, (i ,v) => {
          if (v) {
            vm.data.semester.push({id: v});
          }
        });
      }
    }, true);

    activate();

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;

        if (vm.data.semester) {
          $.each(vm.data.semester, (i ,v) => {
            if (v) {
              vm.semester_ids[v.id] = v.id;
            }
          });
        }
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }
  }
})();

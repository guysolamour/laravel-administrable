@push('js')

<script>
    (function(){

    const deleteButton = document.querySelector('#delete-all')
    // const checked = document.querySelectorAll("[data-check]:checked")

    document.querySelector('#check-all').addEventListener('click', function(){
        document.querySelectorAll('[data-check]').forEach((item) => {
            if (this.checked) {
                item.checked = true;
                showDeleteButton()
            } else {
                item.checked = false;
                hideDeleteButton()
            }
        })
    })

    document.querySelectorAll('[data-check]').forEach(element => {
        element.addEventListener('click', function(){
            if (this.checked) {
                showDeleteButton()
            } else {
                if (document.querySelectorAll("[data-check]:checked").length === 0) {
                    hideDeleteButton()
                }
            }

            if (document.querySelectorAll("[data-check]:checked").length  < document.querySelectorAll("[data-check]").length) {
                document.querySelector('#check-all').checked = false;
            }else {
                document.querySelector('#check-all').checked = true;
            }
        })
    })

    function showDeleteButton(){
        deleteButton.classList.remove('d-none')
        deleteButton.classList.add('d-block')
    }

    function hideDeleteButton(){
        deleteButton.classList.remove('d-block')
        deleteButton.classList.add('d-none')
    }




    deleteButton.addEventListener('click', function(event){

        event.preventDefault()

        swal({
          title: 'Suppression',
          text: 'Etes vous sûr de vouloir supprimer les éléments sélectionnés ?',
          icon: "warning",
          dangerMode: true,
          buttons: {
            cancel: 'Annulez',
            confirm: 'Confirmez!'
          }
        })
          .then((isConfirm) => {
             if (isConfirm) {
                const checked = Array.from(document.querySelectorAll("[data-check]:checked"))
                const ids = checked.map(item => item.getAttribute('data-id'))

                axios.post(`/{{ config('administrable.auth_prefix_path') }}/model/destroy/all`,{ model: deleteButton.getAttribute('data-model'),ids })
                .then(({data}) => {
                    checked.map(item => item.checked = false)
                    window.location.reload()
                }).catch((err) => {
                    alert('Une erreur empêche la Suppression')
                });
            }
          })


    })
      })()


</script>



@endpush

<template>
    <slot></slot>
    <form method="POST" :action="action" :id="id">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" :value="csrfToken">
    </form>
</template>

<script type="text/javascript">
    export default {
        props:['action','actionElement','csrfToken'],
        computed: {
            id(){
                return `form-delete-action_${this.actionElement}`
            }
        },
        ready(){
            var actionElement = this.actionElement, formId = this.id

            $(document).ready(() => {
                $(`#${actionElement}`).click((event) => {
                    event.preventDefault()
                    $(`#${formId}`).submit()
                })
            })
        }
    }
</script>

<style type="text/css" scoped>
    form{
        display: none;
    }
</style>
<script>
  import Editor from './components/Editor.svelte';
</script>

<main>
  <Editor />
</main>

<style>
  :global(body) {
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
  
  main {
    width: 100%;
    height: 100vh;
  }
</style>
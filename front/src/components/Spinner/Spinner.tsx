export const Spinner = () => {
  return (
    <div className="flex items-center justify-center">
      <div className="animate-spin inline-block w-6 h-6 border-[3px] border-current border-t-transparent text-accent rounded-full" role="status" aria-label="loading">
        <span className="sr-only">Loading...</span>
      </div>
    </div>
  )
}
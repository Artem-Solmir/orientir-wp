type SiteSearchFormProps = {
  className: string;
  inputId: string;
};

export function SiteSearchForm({ className, inputId }: SiteSearchFormProps) {
  return (
    <form action="/search" role="search" className={className}>
      <label className="label" htmlFor={inputId} hidden>
        Поиск по сайту
      </label>
      <input
        id={inputId}
        className="input"
        type="search"
        name="s"
        placeholder="Поиск по сайту"
        maxLength={100}
        autoComplete="off"
      />
      <button type="submit" className="btn btn--secondary">
        Найти
      </button>
    </form>
  );
}
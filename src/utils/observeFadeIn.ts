export function observeFadeIn(
  selector: string,
  options?: IntersectionObserverInit
): void {
  const elements = document.querySelectorAll<HTMLElement>(selector);
  if (!elements.length) return;

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.2,
      rootMargin: '0px 0px -50px 0px',
      ...options
    }
  );

  elements.forEach(el => observer.observe(el));
}

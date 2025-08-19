







// const hamburger = document.getElementById('hamburger');
// const navLinks = document.getElementById('nav-links');

//   hamburger.addEventListener('click', () => {
//     navLinks.classList.toggle('active');
//   });

  const fadeIns = document.querySelectorAll('.fade-in');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });


  fadeIns.forEach(card => observer.observe(card));


const modal = document.getElementById('policyModal');
const openBtn = document.getElementById('openPolicies');
const closeBtn = modal.querySelector('.close');
const tabLinks = document.querySelectorAll('.tablink');
const tabContents = document.querySelectorAll('.tabcontent');

openBtn.addEventListener('click', () => {
  modal.classList.remove('hidden');
});

closeBtn.addEventListener('click', () => {
  modal.classList.add('hidden');
});

window.addEventListener('click', (e) => {
  if (e.target === modal) modal.classList.add('hidden');
});

tabLinks.forEach(link => {
  link.addEventListener('click', () => {
    tabLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');
    const target = link.getAttribute('data-tab');
    tabContents.forEach(c => {
      c.classList.remove('active');
      if (c.id === target) c.classList.add('active');
    });
  });
});

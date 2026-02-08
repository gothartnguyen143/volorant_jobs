class IntroPageManager {
  constructor() {
    this.startIntro()
  }

  startIntro() {
    const intro = document.getElementById("page-intro")
    const rightSideContainer = intro.querySelector(".QUERY-right-side-container")
    const leftSideContainer = intro.querySelector(".QUERY-left-side-container")
    rightSideContainer.classList.add("page-is-loaded")
    leftSideContainer.classList.add("page-is-loaded")
  }
}

new IntroPageManager()

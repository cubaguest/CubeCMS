function Product(t,dim,lab,img,prWin,prDoor) {
    this.type = t;
    this.dimension = dim;
    this.label = lab;
    this.image = img;
    this.profileDoor = prDoor;
    this.profileWindow = prWin;

    // prvky zvolené
    this.count = null;
    this.gridType = null;
    this.gridTypeName = null;
    this.insideColorType = null;
    this.insideColorName = null;
    this.insideColorImage = null;
    this.outsideColorType = null;
    this.outsideColorName = null;
    this.outsideColorImage = null;
    this.profileDoorType = null;
    this.profileDoorImage = null;
    this.profileDoorName = null;
    this.profileWindowType = null;
    this.profileWindowImage = null;
    this.profileWindowName = null;
}

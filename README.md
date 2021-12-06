# spaceship-earth
Epcot + WebGL = Spaceship Earth

# Research
To make this happen I had to do some quick research.

## The Target
[Spaceship Earth (Epcot)](https://en.wikipedia.org/wiki/Spaceship_Earth_(Epcot)) has the following information that provided the best source of a starting point:
> Geometrically, Spaceship Earth is derived from the Class 2 geodesic polyhedron with frequency of division equal to 8. Each face of the polyhedron is divided into three isosceles triangles to form each point. In theory, there are 11,520 total isosceles triangles forming 3840 points. In reality, some of those triangles are partially or fully nonexistent due to supports and doors; there are actually only 11,324 silvered facets, with 954 partial or full flat triangular panels.[8]

## Making the OBJ
Grabbed myself a copy of [Blender](https://www.blender.org/download/), activated the [Geodesic Domes](https://docs.blender.org/manual/en/latest/addons/add_mesh/geodesic_domes.html) Add-on, and proceeded to do the following:
- Add Mesh -> Geodesic Dome
- Object: Geodesic
- Class: Class 2
- Hedron: Icosahedron
- Point ^: PointUp
- Shape: tri
- Round: spherical
- Frequency: 8
- Radius: 25

Once my object was created and centered at 0,0,0 I exported it as [spaceship-earth-25.obj](spaceship-earth-25.obj)

## Dimpling
The provided [spaceship-earth-25.obj](spaceship-earth-25.obj) was a complete geodesic polyhedron, so I had to further subdivide the faces into additional triangles, and those supplimentary faces needed to be offset to create the known dimpling on the structure.

[dimplify.php](dimplify.php) was created to do the following:
- Import all vertexes and faces
- Subdivide the faces and offset the newly created faces by 0.5 to create the dimples
- Output as a wavefront OBJ file

Thus [spaceship-earth-dimpled-25_0.5.obj](spaceship-earth-dimpled-25_0.5.obj) was created.

## Re-vt/vn
Plugging [spaceship-earth-dimpled-25_0.5.obj](spaceship-earth-dimpled-25_0.5.obj) back into blender to attach a texture to it and regenerate the normals.

I created [silver_64x64.png](silver_64x64.png) as a texture to apply to the object.

Plugging both back into blender and following a [simple youtube video](https://youtu.be/r5YNJghc81U) to unwrapping we get our vt texture maps.

The end result is [spaceship-earth.obj](spaceship-earth.obj)

# WebGL
Using [three.js](https://threejs.org/) we'll be importing in spaceship-earth.obj and placing lights at all the geodesic points.

[index.html](index.html) is where the show starts!

All the coordinates where the [PointLight](https://threejs.org/docs/#api/en/lights/PointLight)s will exist are stored as vertexes in the pre-dimlified [spaceship-earth-25.obj](spaceship-earth-25.obj)
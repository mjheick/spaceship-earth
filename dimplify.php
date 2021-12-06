<?php
/**
 * This scripts intention is to take a Geodesic Dome created in 
 * Blender and add the Spaceship Earth triangular dimples upon it.
 */
ini_set('memory_limit', '1G');
$input_filename = 'spaceship-earth-25.obj';
$dimple_outtie = 0.5; /* From radius, how out we go */
$output_filename = 'spaceship-earth-dimpled-25_' . $dimple_outtie . '.obj';

/* We're reading only vertexes and faces of a wavefront obj file */
$vertex = [];
$vertex[] = [
	'x' => null,
	'y' => null,
	'z' => null,
]; /* wavefront vertexes are based at 1, so we fill 0 with nothing cause it'll never be used */
$face = [];

/* Read the file, expecting only one object. Too lazy to make this fancy */
$fp = fopen($input_filename, 'rt');
if (!$fp) { die('derp, cannot open ' . $input_filename); }
while (!feof($fp))
{
	$line = trim(fgets($fp));
	list($command) = explode(' ', $line);
	if (strlen($command) > 0)
	{
		if ($command == 'v')
		{
			list($command, $x, $y, $z) = explode(' ', $line);
			$points = ['x' => $x, 'y' => $y, 'z' => $z];
			$vertex[] = $points;
		}
		if ($command == 'f')
		{
			list($command, $faces) = explode(' ', $line);
			if (strpos($faces, '/') === false)
			{
				/* in this current implementation this should never happen, but cause things and stuff... */
				echo "- something that shouldn't happen is happening with faces, line reads '$line'\n";
				list($command, $f1, $f2, $f3) = explode(' ', $line);
				$face[] = [$f1, $f2, $f3];
			}
			else
			{
				/* Format is v/vt/vn. We only care about vertex */
				list($command, $f1, $f2, $f3) = explode(' ', $line);
				list($f1) = explode('/', $f1);
				list($f2) = explode('/', $f2);
				list($f3) = explode('/', $f3);
				$face[] = [$f1, $f2, $f3];
			}
		}
	}
}
fclose($fp);
print "loaded vertexes: " . (count($vertex) - 1) . "\n";
print "loaded faces: " . count($face) . "\n";

/* find midpoint of each face and create a new vertex for it */
$current_faces = count($face);
for ($f = 0; $f < $current_faces; $f++)
{
	/* Get XYZ of faces */
	$f1vtx = $vertex[$face[$f][0]];
	$f2vtx = $vertex[$face[$f][1]];
	$f3vtx = $vertex[$face[$f][2]];

	/* Midpoint calculation of all faces */
	$mx = ($f1vtx['x'] + $f2vtx['x'] + $f3vtx['x']) / 3;
	$my = ($f1vtx['y'] + $f2vtx['y'] + $f3vtx['y']) / 3;
	$mz = ($f1vtx['z'] + $f2vtx['z'] + $f3vtx['z']) / 3;

	/* Normalize midpoint, then Dimple this out */
	$distance = sqrt(($mx * $mx) + ($my * $my) + ($mz * $mz));
	$mx = ($mx / $distance) * (25 + $dimple_outtie);
	$my = ($my / $distance) * (25 + $dimple_outtie);
	$mz = ($mz / $distance) * (25 + $dimple_outtie);

	/* Format midpoint to 6 decimal places */
	$mx = number_format($mx, 6);
	$my = number_format($my, 6);
	$mz = number_format($mz, 6);

	/* The next added vertex will be count($vertex), so we need this when adding faces */
	$mvtx = count($vertex);
	/* Add the vertex to the list */
	$vertex[] = ['x' => $mx, 'y' => $my, 'z' => $mz];

	/* draw 3 more triangles (faces) with the midpoint: 0m1,1m2,2m0 */
	$face[] = [ $face[$f][0], $mvtx, $face[$f][1] ];
	$face[] = [ $face[$f][1], $mvtx, $face[$f][2] ];
	$face[] = [ $face[$f][2], $mvtx, $face[$f][0] ];
}

print "created vertexes: " . (count($vertex) - 1) . "\n";
print "created faces: " . count($face) . "\n";

/* Write File */
file_put_contents($output_filename, "o Epcot_mesh\n");
for ($vtx = 1; $vtx < count($vertex); $vtx++)
{
	file_put_contents($output_filename, 'v ' . $vertex[$vtx]['x'] . ' ' . $vertex[$vtx]['y'] . ' ' . $vertex[$vtx]['z'] . "\n", FILE_APPEND);
}
file_put_contents($output_filename, "usemtl None\ns off\n", FILE_APPEND);

for ($f = 0; $f < count($face); $f++)
{
	file_put_contents($output_filename, 'f ' . $face[$f][0] . ' ' . $face[$f][1] . ' ' . $face[$f][2] . "\n", FILE_APPEND);
}